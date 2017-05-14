<?php

namespace App\Http\Controllers;

use Hash;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
	public function index($username)
	{
		$user = User::whereUsername($username)->first();

		if ( $user === null ) {
			return abort(404);
		}

		return view('profile.index', compact(
			'user'
		));
	}

	public function products($username)
	{
		$user = User::whereUsername($username)->first();

		if ( $user === null ) {
			return abort(404);
		}

		$sales = $user->sales()->latest()->get();

		return view('profile.products', compact(
			'user',
			'sales'
		));
	}

	public function settings($username)
	{
		$user = auth()->user();

		if ( $user === null ) {
			return abort(404);
		}

		if ( $username !== $user->username ) {
			return abort(404);
		}

		return view('profile.settings', [
			'user' => auth()->user()
		]);
	}

	public function settingsGeneral(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'name' => 'max:30',
			'biography' => 'max:200',
			'birthday' => 'date|nullable',
			'location' => 'max:50'
		])->setAttributeNames([
			'name' => __('Adınız & Soyadınız'),
			'biography' => __('Hakkınızda'),
			'birthday' => __('Doğum Tarihiniz'),
			'location' => __('Konum')
		])->validate();

		$user = auth()->user();

		$user->update([
			'name' => $request->name,
			'biography' => $request->biography,
			'location' => $request->location,
			'birthday' => $request->birthday
		]);

		return redirect()->route('profile.settings', $user->username)
			->with('flash.success', __('Kişisel ayarlar başarıyla kaydedildi.'));
	}

	public function settingsSocial(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'social_facebook' => 'max:50',
			'social_youtube' => 'max:50',
			'social_steam' => 'max:50',
		])->setAttributeNames([
			'social_facebook' => __('Facebook'),
			'social_youtube' => __('YouTube'),
			'social_steam' => __('Steam'),
		])->validate();

		$user = auth()->user();

		$user->update([
			'social_facebook' => $request->social_facebook,
			'social_youtube' => $request->social_youtube,
			'social_steam' => $request->social_steam
		]);

		return redirect()->route('profile.settings', $user->username)
			->with('flash.success', __('Sosyal medya ayarları başarıyla kaydedildi.'));
	}

	public function settingsPassword(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'password' => 'required',
			'new_password' => 'min:6|confirmed'
		])->setAttributeNames([
			'password' => __('Mevcut Şifre'),
			'new_password' => __('Yeni Şifre'),
			'new_password_confirmation' => __('Yeni Şifre Tekrarı')
		])->validate();

		$user = auth()->user();

		if ( Hash::check($request->password, $user->password) !== true ) {
			return redirect()->route('profile.settings', $user->username)
				->with('flash.error', __('Mevcut şifreniz doğru değil.'));
		}

		$user->update(['password' => bcrypt($request->new_password)]);

		return redirect()->route('profile.settings', $user->username)
			->with('flash.success', __('Şifreniz başarıyla değiştirildi.'));
	}
}