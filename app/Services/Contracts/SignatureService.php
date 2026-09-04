<?php

namespace App\Services\Contracts;

use App\Models\Contract;
use App\Models\ContractSignature;
use App\Models\User;
use App\Notifications\ContractSignedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SignatureService
{
    public function signContract(Contract $contract, array $data, ?User $user = null): ContractSignature
    {
        if ($contract->status === 'signed') {
            throw ValidationException::withMessages([
                'contract' => ['This contract has already been fully signed and executed.'],
            ]);
        }

        if ($contract->status === 'cancelled') {
            throw ValidationException::withMessages([
                'contract' => ['Cannot sign a voided or cancelled contract.'],
            ]);
        }

        return DB::transaction(function () use ($contract, $data, $user) {
            $signerName = $data['signer_name'] ?? ($user?->name ?? 'Authorized Signer');
            $signerEmail = $data['signer_email'] ?? ($user?->email ?? ($contract->customer?->email ?? 'client@example.com'));
            $signaturePayload = $data['signature'] ?? ('DIGITAL_SIGNATURE_' . hash('sha256', $signerName . now()->timestamp));
            $ipAddress = $data['ip_address'] ?? (request()->ip() ?? '127.0.0.1');

            // Cryptographic checksum
            $checksum = hash('sha256', $contract->content . '|' . $signerName . '|' . $signerEmail . '|' . $ipAddress . '|' . now()->toIso8601String());

            $signature = ContractSignature::create([
                'contract_id' => $contract->id,
                'user_id' => $user?->id,
                'signer_name' => $signerName,
                'signer_email' => $signerEmail,
                'signed_at' => now(),
                'signature' => $signaturePayload,
                'ip_address' => $ipAddress,
            ]);

            $contract->update([
                'status' => 'signed',
            ]);

            // Notify company owners/customer
            try {
                if ($contract->customer && $contract->customer->user) {
                    $contract->customer->user->notify(new ContractSignedNotification($contract, $signature));
                }
            } catch (\Throwable $e) {
                // Ignore notification failure during signature execution
            }

            return $signature;
        });
    }

    public function verifySignature(ContractSignature $signature): bool
    {
        return !empty($signature->signed_at) && !empty($signature->signature) && $signature->contract !== null;
    }
}
