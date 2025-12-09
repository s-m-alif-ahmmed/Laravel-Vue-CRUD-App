<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Traits\AllTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileUpdateController extends Controller
{
    use AllTraits;

    public function changeEmail(Request $request)
    {
        $validator = $request->validate([
            'password' => 'required|string',
            'email' => 'required|string|confirmed',
        ]);

        if (!$validator) {
            return $this->error($validator->errors(), 422);
        }

        try {

            $user = auth()->user();

            if (!Hash::check($request->password, $user->password)) {
                return $this->error('Incorrect Password', 402);
            }

            $user->email = $request->email;
            $user->save();

            return $this->ok('Email changed successfully');

        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        if (!$validator) {
            return $this->error($validator->errors(), 422);
        }

        try {

            $user = auth()->user();

            if (!Hash::check($request->old_password, $user->password)) {
                return $this->error('Incorrect Current Password', 402);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return $this->ok('Password changed successfully');

        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function profileAvatarUpload(Request $request)
    {
        $validator = $request->validate([
            'avatar' => 'image|max:2048',
        ]);

        if (!$validator) {
            return $this->error('Validation failed', 422);
        }

        try {

            $user = auth()->user();

            if ($request->hasFile('avatar')) {
                $imagePath = $this->uploadToPublic($request->avatar, 'uploads/avatar');
                $user->avatar = $imagePath;
            }

            $user->save();

            return $this->ok('Profile image upload successfully');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function profileAvatarRemove()
    {
        try {

            $user = auth()->user();

            if ($user->avatar) {
                $this->deleteFromPublic($user, 'avatar');

                $user->avatar = null;
                $user->save();

                return $this->ok('Profile image removed successfully');
            }

            return $this->error('Profile image not found', 404);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function updateDetails(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "name"          => 'nullable|string',
            "email"         => 'nullable|string|email|unique:users,email,' . Auth::id(),
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 422);
        }

        $user = Auth::user();

        $update = $user->update([
            "name"     => $request->name ?? $user->name,
            "email"    => $request->email ?? $user->email,
        ]);

        if ($update) {
            return $this->success('Profile information successfully updated.', $user, 200);
        } else {
            return $this->error('Failed to update profile.', 500);
        }
    }

}
