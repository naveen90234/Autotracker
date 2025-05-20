<?php

namespace App\Imports;

use App\Models\MaintenanceTaskType;
use App\Models\CarPart;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;


class MaintenanceImport implements ToModel, WithHeadingRow // ADD WithHeadingRow
{
    public function model(array $row)
    {
        return new MaintenanceTaskType([
            'title' => $row['title'], // Now Laravel will correctly map the 'name' column
        ]);
    }
}
