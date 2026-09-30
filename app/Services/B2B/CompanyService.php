<?php

namespace App\Services\B2B;

use App\Models\Company;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CompanyService
{
    public function create(array $data): Company
    {
        $data = validator($data, [
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:50|unique:companies,code',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms_days' => 'nullable|integer|min:0|max:365',
            'credit_limit' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ])->validate();

        return Company::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'tax_id' => $data['tax_id'] ?? null,
            'payment_terms_days' => (int) ($data['payment_terms_days'] ?? 0),
            'credit_limit' => (int) round((float) ($data['credit_limit'] ?? 0)),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
    }

    public function attachUser(Company $company, User $user, string $role = 'staff', bool $canApprove = false): void
    {
        if (! in_array($role, ['owner', 'staff'], true)) {
            throw ValidationException::withMessages(['role' => 'Peran harus owner atau staff.']);
        }

        $company->users()->syncWithoutDetaching([
            $user->id => ['role' => $role, 'can_approve' => $canApprove],
        ]);
    }

    public function activeCompanyFor(User $user): ?Company
    {
        return $user->companies()->where('companies.is_active', true)->orderByDesc('company_user.id')->first();
    }

    public function canApprove(User $user, Company $company): bool
    {
        $row = $company->users()->where('users.id', $user->id)->first();

        return $row && ((bool) $row->pivot->can_approve || $row->pivot->role === 'owner');
    }
}
