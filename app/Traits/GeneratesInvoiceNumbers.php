<?php

namespace App\Traits;

use App\Models\Invoice;
use UnexpectedValueException;

trait GeneratesInvoiceNumbers
{
    /**
     * Generate the next sequential invoice number.
     * 
     * @param string $prefix The prefix including date parts (e.g., 'INV-20240326' or 'INV/2024/03')
     * @param string $separator The separator before the 4-digit sequence (default: '-')
     * @return string
     * @throws UnexpectedValueException
     */
    protected function generateNextInvoiceNumber(string $prefix, string $separator = '-'): string
    {
        $lastInvoice = Invoice::where('invoice_number', 'like', "{$prefix}{$separator}%")
            ->latest('id')
            ->lockForUpdate()
            ->first();

        $nextNumber = 1;
        if ($lastInvoice) {
            $lastNumberStr = substr($lastInvoice->invoice_number, -4);
            if (!is_numeric($lastNumberStr)) {
                throw new UnexpectedValueException("Tidak dapat menentukan nomor invoice berikutnya dari nomor yang salah format: {$lastInvoice->invoice_number}");
            }
            $nextNumber = (int) $lastNumberStr + 1;
        }

        return $prefix . $separator . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
