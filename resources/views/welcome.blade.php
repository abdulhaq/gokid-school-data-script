<?php

use App\Models\OrgMembers;
use PhpOffice\PhpSpreadsheet\IOFactory;

// $file = Storage::get($filename);
// $filePath = $file->path();
$filePath = public_path() . '/school-data.csv';

$spreadsheet = IOFactory::load($filePath);
$worksheet = $spreadsheet->getActiveSheet();

// Get the highest row number and column letter
$highestRow = $worksheet->getHighestRow();
$highestColumn = $worksheet->getHighestColumn();

$html = '<table border=1 id="table_detail" align=center cellpadding=10>
<tr>
    <th>File</th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
    <th>Database</th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
</tr>    
<tr>
    <th>Name</th>
    <th>Role</th>
    <th>Address</th>
    <th>Email</th>
    <th>Phone</th>
    <th></th>
    <th></th>
    <th>Name</th>
    <th>Role</th>
    <th>Address</th>
    <th>Email</th>
    <th>Phone</th>
  </tr>';


for ($row = 2; $row <= $highestRow; $row++) {
    $parent_first_name = $worksheet->getCell('A' . $row)->getValue();
    $parent_last_name = $worksheet->getCell('B' . $row)->getValue();
    $student_1_fname = $worksheet->getCell('C' . $row)->getValue();
    $student_1_lname = $worksheet->getCell('D' . $row)->getValue();
    $student_1_grade = $worksheet->getCell('E' . $row)->getValue();
    $student_2_fname = $worksheet->getCell('F' . $row)->getValue();
    $student_2_lname = $worksheet->getCell('G' . $row)->getValue();
    $student_2_grade = $worksheet->getCell('H' . $row)->getValue();
    $student_3_fname = $worksheet->getCell('I' . $row)->getValue();
    $student_3_lname = $worksheet->getCell('J' . $row)->getValue();
    $student_3_grade = $worksheet->getCell('K' . $row)->getValue();
    $address_1 = $worksheet->getCell('L' . $row)->getValue();
    $address_2 = $worksheet->getCell('M' . $row)->getValue();
    $city = $worksheet->getCell('N' . $row)->getValue();
    $state = $worksheet->getCell('O' . $row)->getValue();
    $zip = $worksheet->getCell('P' . $row)->getValue();
    $county = $worksheet->getCell('Q' . $row)->getValue();
    $email = $worksheet->getCell('R' . $row)->getValue();
    $phone = $worksheet->getCell('S' . $row)->getValue();

    $parent_name = $parent_first_name . ' ' . $parent_last_name;
    $student_1_name = $student_1_fname . ' ' . $student_1_lname;
    $student_2_name = $student_2_fname . ' ' . $student_2_lname;
    $student_3_name = $student_3_fname . ' ' . $student_3_lname;
    $address = $address_1 . ', ' . $address_2 . ', ' . $city . ', ' . $state . ', ' . $zip . ', ' . $county;

    $member = OrgMembers::where('email', $email)->first();

    if (isset($member)) {

        // dd($member);
        $family_members = OrgMembers::where('organization_family_id', $member->organization_family_id)->orderBy('role', 'asc')->get();
        // dd($family_members);

        $i = 0;
        foreach ($family_members as $fmember) {
            $i++;
            if ($fmember->role == 1) {
                $html .= '<tr class="parent">
                    <td>' . $parent_name . '</td>
                    <td>Parent</td>
                    <td>' . $address . '</td>
                    <td>' . $email . '</td>
                    <td>' . $phone . '</td>
                    <td></td>
                    <td></td>
                    <td>'.$member->organization_family_id.'</td>
                    <td>' . $fmember->first_name . ' ' . $fmember->last_name . '</td>
                    <td>Parent</td>
                    <td>' . $address . '</td>
                    <td>' . $fmember->email . '</td>
                    <td>' . $fmember->phone_number . '</td>
                </tr>';
            } else {

                if ($i == 1) {
                    $student_name = $student_1_name;
                    $student_grade = $student_1_grade;
                } elseif ($i == 2) {
                    $student_name = $student_2_name;
                    $student_grade = $student_2_grade;
                } elseif ($i == 3) {
                    $student_name = $student_3_name;
                    $student_grade = $student_3_grade;
                } else {
                    $student_name = 'Name more than 3';
                    $student_grade = 'Grade more than 3';
                }
                $html .= '<tr class="child">
                    <td>' . $student_name . '</td>
                    <td>Child</td>
                    <td>' . $address . '</td>
                    <td>' . $student_grade . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>' . $fmember->first_name . ' ' . $fmember->last_name . '</td>
                    <td>Child</td>
                    <td>' . $address . '</td>
                    <td>' . $fmember->organization_grade_id . '</td>
                    <td></td>
                </tr>';
            }
        }
    } else {
        $html .= '<tr class="parent">
        <td>' . $parent_name . '</td>
        <td>Parent</td>
        <td>' . $address . '</td>
        <td>' . $email . '</td>
        <td>' . $phone . '</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>';

        if (isset($student_1_name)) {
            $html .= '<tr class="child">
        <td>' . $student_1_name . '</td>
        <td>Child</td>
        <td></td>
        <td>Grade: ' . $student_1_grade . '</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>';
        }

        if (isset($student_2_name)) {
            $html .= '<tr class="child">
        <td>' . $student_2_name . '</td>
        <td>Child</td>
        <td></td>
        <td>Grade: ' . $student_2_grade . '</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>';
        }

        if ($student_3_name != '') {
            $html .= '<tr class="child">
        <td>' . $student_3_name . '</td>
        <td>Child</td>
        <td></td>
        <td>Grade: ' . $student_3_grade . '</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>';
        }
    }
}
$html .= '</table>';
echo $html;
// return redirect()->route('match-trans-ids.usps_upload_rta_view', ['id' => 2])->with('success', 'RTA uploaded successfully!');
?>

<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td,
    th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        /*background-color: #dddddd; */
    }

    tr.parent {
        background-color: #dddddd;
    }

    tr.child {
        background-color: #ffffff;
    }

    td:nth-child(3),
    td:nth-child(10) {
        display: none;
    }

    th:nth-child(3),
    th:nth-child(10) {
        display: none;
    }
</style>