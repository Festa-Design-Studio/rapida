<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function showRegister(Request $request): View
    {
        $reportId = $request->query('report');
        $crisis = Crisis::where('status', 'active')->first();

        return view('auth.reporter-register', [
            'reportId' => $reportId,
            'crisis' => $crisis,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'confirmed'],
            'language' => ['nullable', 'string', 'max:5'],
        ]);

        $emailHash = hash('sha256', strtolower(trim($validated['email'])));

        // Check if account already exists
        $existing = Account::where('phone_or_email_hash', $emailHash)->first();
        if ($existing) {
            return back()->withErrors(['email' => __('account.already_exists')])->withInput();
        }

        $crisis = Crisis::where('status', 'active')->first();

        $account = Account::create([
            'phone_or_email_hash' => $emailHash,
            'password' => $validated['password'], // Model casts to hashed
            'crisis_id' => $crisis?->id,
            'preferred_language' => $validated['language'] ?? session('locale', 'en'),
        ]);

        // Link any recent anonymous report to this account
        $reportId = $request->input('report_id');
        if ($reportId) {
            DamageReport::where('id', $reportId)
                ->whereNull('account_id')
                ->update(['account_id' => $account->id]);
        }

        Auth::guard('web')->login($account);

        return redirect()->route('account.profile')
            ->with('success', __('account.welcome'));
    }

    public function showLogin(): View
    {
        return view('auth.reporter-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $emailHash = hash('sha256', strtolower(trim($validated['email'])));
        $account = Account::where('phone_or_email_hash', $emailHash)->first();

        if ($account && Hash::check($validated['password'], $account->password)) {
            Auth::guard('web')->login($account, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('account.profile'));
        }

        return back()->withErrors([
            'email' => __('account.invalid_credentials'),
        ])->onlyInput('email');
    }

    public function profile(): View
    {
        $account = Auth::guard('web')->user();
        $reports = DamageReport::where('account_id', $account->id)
            ->latest('submitted_at')
            ->get();
        $badges = $account->badges()->latest('awarded_at')->get();
        $crisis = Crisis::where('status', 'active')->first();
        $communityCount = $crisis
            ? DamageReport::where('crisis_id', $crisis->id)->count()
            : 0;

        return view('auth.reporter-profile', [
            'account' => $account,
            'reports' => $reports,
            'badges' => $badges,
            'communityCount' => $communityCount,
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('map-home');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $account = Auth::guard('web')->user();

        // GDPR: unlink reports (keep them as anonymous) and delete account
        DamageReport::where('account_id', $account->id)->update(['account_id' => null]);
        $account->badges()->delete();
        $account->delete();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('map-home')
            ->with('success', __('account.deleted'));
    }
}
