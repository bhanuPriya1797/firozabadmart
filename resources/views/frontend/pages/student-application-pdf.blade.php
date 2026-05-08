<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application {{ $application->application_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .container { width: 100%; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Cuttack District Archery Association</h2>
            <p>Application Details</p>
            <p>Application #: {{ $application->application_number }}</p>
        </div>

        <div>
            <div class="section-title">Student Information</div>
            <table>
                <tr>
                    <th>Name</th>
                    <td>{{ $application->student->first_name }} {{ $application->student->surname }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $application->student->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $application->student->contact_no ?: $application->student->phone }}</td>
                </tr>
            </table>
        </div>

        <div>
            <div class="section-title">Course Information</div>
            <table>
                <tr>
                    <th>Course Name</th>
                    <td>{{ $application->course_name }}</td>
                </tr>
                <tr>
                    <th>College</th>
                    <td>{{ $application->college_name }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ $application->status_text }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>

