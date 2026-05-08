<?php

namespace App\Exports;

use App\Models\Archer;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArcherExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $appsSub = DB::table('archer_applications as aa')
            ->select('aa.archer_id', 'aa.status')
            ->join(DB::raw('(SELECT archer_id, MAX(id) AS last_id FROM archer_applications GROUP BY archer_id) AS t'), 'aa.id', '=', 't.last_id');

        $query = Archer::query()
            ->leftJoinSub($appsSub, 'apps', function ($join) {
                $join->on('apps.archer_id', '=', 'archers.id');
            })
            ->select('archers.*', 'apps.status as application_status');

        if (isset($this->filters['app_status']) && $this->filters['app_status'] !== '') {
            $status = $this->filters['app_status'] === 'affiliated' ? 'approved' : $this->filters['app_status'];
            $query->where('application_status', $status);
        }

        if (isset($this->filters['search']) && $this->filters['search'] !== '') {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('surname', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if (isset($this->filters['status']) && $this->filters['status'] !== '') {
            $query->where('status', (int)$this->filters['status']);
        }
        if (isset($this->filters['gender']) && $this->filters['gender'] !== '') {
            $query->where('gender', $this->filters['gender']);
        }

        return $query->orderBy('archers.id', 'desc')->get([
            'id',
            'first_name',
            'surname',
            'email',
            'phone',
            'whatsapp_number',
            'gender',
            'dob',
            'marital_status',
            'is_minor',
            'category',
            'member_id',
            'member_association',
            'aadhar_card_number',
            'application_status',
            'status',
            'created_at',
        ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Surname',
            'Email',
            'Phone',
            'WhatsApp',
            'Gender',
            'DOB',
            'Marital Status',
            'Is Minor',
            'Category',
            'Member ID',
            'Member Association',
            'Aadhaar No',
            'Application Status',
            'Status',
            'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
