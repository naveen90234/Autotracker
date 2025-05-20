<?php

namespace App\Imports;

use App\Models\CarPart;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // ADD THIS

class CarPartImport implements ToModel, WithHeadingRow // ADD WithHeadingRow
{
    public function model(array $row)
    {
        return new CarPart([
            'name' => $row['name'], // Now Laravel will correctly map the 'name' column
        ]);
    }
}
