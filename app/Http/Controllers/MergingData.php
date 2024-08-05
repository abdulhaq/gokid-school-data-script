<?php

namespace App\Http\Controllers;

use App\Models\Addresses;
use App\Models\OrgFamilies;
use App\Models\OrgGrades;
use App\Models\OrgMembers;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

use function PHPUnit\Framework\isNull;

class MergingData extends Controller
{
    public function getCurrentLive()
    {
        // Create a new spreadsheet instance
        $spreadsheet = new Spreadsheet();

        // Get the active sheet (first sheet)
        $sheet = $spreadsheet->getActiveSheet();

        // Set cell values
        $sheet->setCellValue('A1', 'Parent First Name');
        $sheet->setCellValue('B1', 'Parent Last Name');
        $sheet->setCellValue('C1', 'Student 1 First Name');
        $sheet->setCellValue('D1', 'Student 1 Last Name');
        $sheet->setCellValue('E1', 'Student 1 Grade');
        $sheet->setCellValue('F1', 'Student 2 First Name');
        $sheet->setCellValue('G1', 'Student 2 Last Name');
        $sheet->setCellValue('H1', 'Student 2 Grade');
        $sheet->setCellValue('I1', 'Student 3 First Name');
        $sheet->setCellValue('J1', 'Student 3 Last Name');
        $sheet->setCellValue('K1', 'Student 3 Grade');
        $sheet->setCellValue('L1', 'Address Line 1');
        $sheet->setCellValue('M1', 'Address Line 2');
        $sheet->setCellValue('N1', 'City');
        $sheet->setCellValue('O1', 'State');
        $sheet->setCellValue('P1', 'Post/Zip Code');
        $sheet->setCellValue('Q1', 'Country');
        $sheet->setCellValue('R1', 'E-Mail');
        $sheet->setCellValue('S1', 'Mobile Phone Number');

        $families = OrgFamilies::where('organization_id', 8)->get();
        // dd($families);

        $row = 2;
        foreach ($families as $family) {
            // dd($family->address);
            // dd($family->address->lines['city']);
            $parents = OrgMembers::where('organization_family_id', $family->id)->where('role', 1)->get();

            foreach ($parents as $parent) {
                $kids = OrgMembers::where('organization_family_id', $family->id)->where('role', 2)->get();
                // dd($kids[0]->first_name);

                $sheet->setCellValue("A{$row}", $parent->first_name);
                $sheet->setCellValue("B{$row}", $parent->last_name);

                $sheet->setCellValue("C{$row}", $kids[0]->first_name);
                $sheet->setCellValue("D{$row}", $kids[0]->last_name);
                $sheet->setCellValue("E{$row}", $kids[0]->grade->order);

                if (isset($kids[1])) {
                    $sheet->setCellValue("F{$row}", $kids[1]->first_name);
                    $sheet->setCellValue("G{$row}", $kids[1]->last_name);
                    $sheet->setCellValue("H{$row}", $kids[1]->grade->order);
                }
                if (isset($kids[2])) {
                    $sheet->setCellValue("I{$row}", $kids[2]->first_name);
                    $sheet->setCellValue("J{$row}", $kids[2]->last_name);
                    $sheet->setCellValue("K{$row}", $kids[2]->grade->order);
                }
                if (isset($family->address->lines)) {
                    if ($family->address->lines['address_line_1'] == null) {
                        $sheet->setCellValue("L{$row}", $family->address->name);
                    } else {
                        $sheet->setCellValue("L{$row}", $family->address->lines['address_line_1']);
                    }
                    $sheet->setCellValue("M{$row}", $family->address->lines['address_line_2']);
                    $sheet->setCellValue("N{$row}", $family->address->lines['city']);
                    $sheet->setCellValue("O{$row}", $family->address->lines['state']);
                    $sheet->setCellValue("P{$row}", $family->address->lines['postal_code']);
                    $sheet->setCellValue("Q{$row}", $family->address->lines['country']);
                } else {
                    // dd($family->address->name);
                    $sheet->setCellValue("L{$row}", $family->address->name);
                }

                $sheet->setCellValue("R{$row}", $parent->email);
                $sheet->setCellValue("S{$row}", $parent->phone_number);
                $row++;
            }
        }

        // Create a writer instance for CSV format
        $writer = new Csv($spreadsheet);

        // Set delimiter if needed (optional, default is comma)
        $writer->setDelimiter(',');

        // Save the file to the specified path
        $filename = 'existing_data.csv';
        $writer->save($filename);

        echo "CSV file written to {$filename}";
    }

    /**
     * This downloads a sheet with all students that are in excel but not in Database
     */
    public function getNewData($school_id)
    {
        // $filePath = public_path() . '/Del_Oro_school.csv';
        $filePath = public_path() . '/' . $school_id . '.csv';

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        // Get the highest row number and column letter
        $highestRow = $worksheet->getHighestRow();
        // dd($highestRow);
        $highestColumn = $worksheet->getHighestColumn();

        // write new sheet
        // Create a new spreadsheet instance
        $sheet2 = new Spreadsheet();

        // Get the active sheet (first sheet)
        $sheet = $sheet2->getActiveSheet();

        // Set cell values
        $sheet->setCellValue('A1', 'Parent First Name');
        $sheet->setCellValue('B1', 'Parent Last Name');
        $sheet->setCellValue('C1', 'Student 1 First Name');
        $sheet->setCellValue('D1', 'Student 1 Last Name');
        $sheet->setCellValue('E1', 'Student 1 Grade');
        $sheet->setCellValue('F1', 'Student 2 First Name');
        $sheet->setCellValue('G1', 'Student 2 Last Name');
        $sheet->setCellValue('H1', 'Student 2 Grade');
        $sheet->setCellValue('I1', 'Student 3 First Name');
        $sheet->setCellValue('J1', 'Student 3 Last Name');
        $sheet->setCellValue('K1', 'Student 3 Grade');
        $sheet->setCellValue('L1', 'Address Line 1');
        $sheet->setCellValue('M1', 'Address Line 2');
        $sheet->setCellValue('N1', 'City');
        $sheet->setCellValue('O1', 'State');
        $sheet->setCellValue('P1', 'Post/Zip Code');
        $sheet->setCellValue('Q1', 'Country');
        $sheet->setCellValue('R1', 'E-Mail');
        $sheet->setCellValue('S1', 'Mobile Phone Number');
        $row2 = 2;

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
            $country = $worksheet->getCell('Q' . $row)->getValue();
            $email = $worksheet->getCell('R' . $row)->getValue();
            $phone = $worksheet->getCell('S' . $row)->getValue();

            $member = OrgMembers::where('email', $email)->with('family')->first();
            // dd($member->family[0]->organization_id);

            if (!isset($member)) {

                $sheet->setCellValue("A{$row2}", $parent_first_name);
                $sheet->setCellValue("B{$row2}", $parent_last_name);

                $sheet->setCellValue("C{$row2}", $student_1_fname);
                $sheet->setCellValue("D{$row2}", $student_1_lname);
                $sheet->setCellValue("E{$row2}", $student_1_grade);


                $sheet->setCellValue("F{$row2}", $student_2_fname);
                $sheet->setCellValue("G{$row2}", $student_2_lname);
                $sheet->setCellValue("H{$row2}", $student_2_grade);


                $sheet->setCellValue("I{$row2}", $student_3_fname);
                $sheet->setCellValue("J{$row2}", $student_3_lname);
                $sheet->setCellValue("K{$row2}", $student_3_grade);

                $sheet->setCellValue("L{$row2}", $address_1);

                $sheet->setCellValue("M{$row2}", $address_2);
                $sheet->setCellValue("N{$row2}", $city);
                $sheet->setCellValue("O{$row2}", $state);
                $sheet->setCellValue("P{$row2}", $zip);
                $sheet->setCellValue("Q{$row2}", $country);

                $sheet->setCellValue("R{$row2}", $email);
                $sheet->setCellValue("S{$row2}", $phone);
                $row2++;
            } else {
                if ($member->family[0]->organization_id != $school_id) {
                    $sheet->setCellValue("A{$row2}", $parent_first_name);
                    $sheet->setCellValue("B{$row2}", $parent_last_name);

                    $sheet->setCellValue("C{$row2}", $student_1_fname);
                    $sheet->setCellValue("D{$row2}", $student_1_lname);
                    $sheet->setCellValue("E{$row2}", $student_1_grade);


                    $sheet->setCellValue("F{$row2}", $student_2_fname);
                    $sheet->setCellValue("G{$row2}", $student_2_lname);
                    $sheet->setCellValue("H{$row2}", $student_2_grade);


                    $sheet->setCellValue("I{$row2}", $student_3_fname);
                    $sheet->setCellValue("J{$row2}", $student_3_lname);
                    $sheet->setCellValue("K{$row2}", $student_3_grade);

                    $sheet->setCellValue("L{$row2}", $address_1);

                    $sheet->setCellValue("M{$row2}", $address_2);
                    $sheet->setCellValue("N{$row2}", $city);
                    $sheet->setCellValue("O{$row2}", $state);
                    $sheet->setCellValue("P{$row2}", $zip);
                    $sheet->setCellValue("Q{$row2}", $country);

                    $sheet->setCellValue("R{$row2}", $email);
                    $sheet->setCellValue("S{$row2}", $phone);

                    $sheet->setCellValue("U{$row2}", 'Exist in another school: ' . $member->family[0]->organization_id);
                    $row2++;
                } else {
                    continue;
                }
            }
        }

        // Create a writer instance for CSV format
        $writer = new Csv($sheet2);

        // Set delimiter if needed (optional, default is comma)
        $writer->setDelimiter(',');

        // Save the file to the specified path
        // Create a temporary file to save the CSV
        $temp_file = tempnam(sys_get_temp_dir(), 'csv');
        $writer->save($temp_file);

        // Set the headers to force a download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="new_data_gus.csv"');
        header('Content-Length: ' . filesize($temp_file));

        // Output the file content
        readfile($temp_file);

        // Delete the temporary file
        unlink($temp_file);

        exit;
    }

    /**
     * Check if address exist for new family. If yes, add new parent to existing family
     */
    public function checkIfAddressExist($address)
    {
        $address = Addresses::where('name', $address)->get();
        if (isset($address)) {
            return true;
        } else {
            return true;
        }
    }

    /**
     * This will return list of students/families that are in the system but not in excel
     */
    public function inDbNotInExcel($school_id)
    {
        $members = OrgFamilies::where('organization_id', $school_id)->with('members')->get();

        $column = 'R';
        // Load the Excel file
        $filePath = public_path() . '/' . $school_id . '.csv';
        $spreadsheet = IOFactory::load($filePath);

        // Get the first sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Get the highest row number in the specified column
        $highestRow = $sheet->getHighestRow();

        $emails = '';
        $count = 0;
        foreach ($members as $member) {
            $found = false;
            foreach ($member->members as $memb) {
                // dd($memb->email);

                if (isset($memb->email)) {
                    for ($row = 0; $row <= $highestRow; $row++) {
                        $cellValue = $sheet->getCell($column . $row)->getValue();
                        if (strcasecmp(trim($cellValue), trim($memb->email)) == 0) {
                            $found = true;
                        }
                    }
                    if (!$found) {
                        $this->deleteFamilyByEmail($memb->email);
                        // dd('deleted: '. $memb->email);
                        $count++;
                        $emails .= $memb->email . '</br>';
                        if ($count == 40) {
                            dd('deleted :' . $emails);
                        }
                    }
                }
            }
        }

        echo $count . '</br>' . $emails; // Return null if no match is found
    }

    /**
     * This will update student grades
     */
    public function updateData($school_id)
    {
        $filePath = public_path() . '/' . $school_id . '.csv';

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        // Get the highest row number and column letter
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        // dd($highestRow);

        if (isset($_GET['from'])) {
            $from = $_GET['from'];
        } else {
            $from = 2;
        }
        if (isset($_GET['till'])) {
            $till = $_GET['till'];
        } else {
            $till = 100;
        }

        for ($row = $from; $row <= $highestRow; $row++) {
            $parent_first_name = $worksheet->getCell('A' . $row)->getValue();
            $parent_last_name = $worksheet->getCell('B' . $row)->getValue();
            $student_1_fname = ltrim($worksheet->getCell('C' . $row)->getValue());
            $student_1_lname = $worksheet->getCell('D' . $row)->getValue();
            $student_1_grade = $worksheet->getCell('E' . $row)->getValue();
            $student_2_fname = ltrim($worksheet->getCell('F' . $row)->getValue());
            $student_2_lname = $worksheet->getCell('G' . $row)->getValue();
            $student_2_grade = $worksheet->getCell('H' . $row)->getValue();
            $student_3_fname = ltrim($worksheet->getCell('I' . $row)->getValue());
            $student_3_lname = $worksheet->getCell('J' . $row)->getValue();
            $student_3_grade = $worksheet->getCell('K' . $row)->getValue();
            $address_1 = $worksheet->getCell('L' . $row)->getValue();
            $address_2 = $worksheet->getCell('M' . $row)->getValue();
            $city = $worksheet->getCell('N' . $row)->getValue();
            $state = $worksheet->getCell('O' . $row)->getValue();
            $zip = $worksheet->getCell('P' . $row)->getValue();
            $country = $worksheet->getCell('Q' . $row)->getValue();
            $email = $worksheet->getCell('R' . $row)->getValue();
            $phone = $worksheet->getCell('S' . $row)->getValue();

            $member = OrgMembers::where('email', $email)->first();
            if (isset($member)) {

                // 1st kid
                if ($student_1_fname != null) {
                    // dd($member);
                    // dd($this->getGrade($student_1_grade));
                    $kid1 = OrgMembers::where('organization_family_id', $member->organization_family_id)
                        ->where('role', 2)
                        ->where('first_name', $student_1_fname)
                        ->where('last_name', $student_1_lname)
                        ->first();
                    if ($kid1) {
                        $kid1->update(['organization_grade_id' => $this->getGrade($student_1_grade, $school_id)]);
                    } else {
                        OrgMembers::create([
                            'organization_family_id' => $member->organization_family_id,
                            'first_name' => $student_1_fname,
                            'last_name' => $student_1_lname,
                            'role' => 2,
                            'organization_grade_id' => $this->getGrade($student_1_grade, $school_id),
                        ]);
                    }
                }

                // 2nd kid
                if ($student_2_fname != null) {
                    $kid2 = OrgMembers::where('organization_family_id', $member->organization_family_id)
                        ->where('role', 2)
                        ->where('first_name', $student_2_fname)
                        ->where('last_name', $student_2_lname)
                        ->first();
                    if ($kid2) {
                        $kid2->update(['organization_grade_id' => $this->getGrade($student_2_grade, $school_id)]);
                    } else {
                        OrgMembers::create([
                            'organization_family_id' => $member->organization_family_id,
                            'first_name' => $student_2_fname,
                            'last_name' => $student_2_lname,
                            'role' => 2,
                            'organization_grade_id' => $this->getGrade($student_2_grade, $school_id),
                        ]);
                    }
                }

                // 3rd kid
                if ($student_3_fname != null) {
                    $kid3 = OrgMembers::where('organization_family_id', $member->organization_family_id)
                        ->where('role', 2)
                        ->where('first_name', $student_3_fname)
                        ->where('last_name', $student_3_lname)
                        ->first();
                    if ($kid3) {
                        $kid3->update(['organization_grade_id' => $this->getGrade($student_3_grade, $school_id)]);
                    } else {
                        OrgMembers::create([
                            'organization_family_id' => $member->organization_family_id,
                            'first_name' => $student_3_fname,
                            'last_name' => $student_3_lname,
                            'role' => 2,
                            'organization_grade_id' => $this->getGrade($student_3_grade, $school_id),
                        ]);
                    }
                }
            }
            // dd($kid2);
            if ($row == $till) {
                dd('50 done');
            }
        }
    }

    public function getGrade($grade, $school_id)
    {
        return OrgGrades::where('organization_id', $school_id)->where('name', $grade)->value('id');
    }

    public function deleteGraduatedKids()
    {
        // find all kids with grade 12 and delete them. Make sure you do this before adding new data and updating old data.

        /**
         * To delete kids, find the last grade of school and search all kids with that grade (this should be done before adding new kids).
         * And match the kids with excel file. If kid is not found in excel, then delete them.
         */

        /**
         * Steps:
         * 1. Delete all kids in 12th grade as they graduated
         * 2. Update all kids grade
         * 3. Upload new kids/families
         */
    }

    /**
     * This deletes a family provided email address of one member
     */
    public function deleteFamilyByEmail($email)
    {
        // Find the family member by email
        $family = OrgMembers::where('email', $email)->first();

        if ($family) {
            // check if family has more than 1 parent. If yes don't delete it
            $parents = OrgMembers::where('organization_family_id', $family->organization_family_id)->where('role', 1)->count();
            if ($parents == 2) {
                // Find the family by the organization_family_id and delete it if it exists
                $orgFamily = OrgFamilies::find($family->organization_family_id);
                if ($orgFamily) {
                    $orgFamily->delete();
                }

                // Delete all members associated with the organization_family_id
                OrgMembers::where('organization_family_id', $family->organization_family_id)->delete();
            } else {
                echo "Family with id {$family->organization_family_id} has more than 1 parent {$email}.</br>";
            }
        } else {
            echo "Family member with email {$email} not found.</br>";
        }
    }

    /**
     * This function displays a list of all families in a school that have same address. Gives option to merge them as one.
     */
    public function familiesWithSameAddress($school_id)
    {
        // $families = OrgFamilies::where('organization_id', $school_id)->get();
        // foreach($families as $family) {

        // }
        // Find shared addresses for org_id = 33
        // $sharedAddresses = DB::table('org_families')
        //     ->join('addresses', 'org_families.address_id', '=', 'addresses.id')
        //     ->select('addresses.name as address_name', DB::raw('count(*) as family_count'))
        //     ->where('org_families.org_id', 33)
        //     ->groupBy('addresses.name')
        //     ->having('family_count', '>', 1)
        //     ->get();

        // Find families with shared addresses for org_id = 33
        $sharedFamilies = OrgFamilies::with('address')
            ->where('organization_id', $school_id)
            ->whereHas('address', function ($query) use ($school_id) {
                $query->whereIn('name', function ($subQuery) use ($school_id) {
                    $subQuery->select('addresses.name')
                        ->from('organization_families')
                        ->join('addresses', 'organization_families.address_id', '=', 'addresses.id')
                        ->where('organization_families.organization_id', $school_id)
                        ->groupBy('addresses.name')
                        ->havingRaw('COUNT(*) > 1');
                });
            })
            ->join('addresses', 'organization_families.address_id', '=', 'addresses.id')
            ->orderBy('addresses.name')
            ->get();

        echo '<style>
                table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
                }

                td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
                }

                tr.dark {
                background-color: #dddddd;
                }
            </style>
            <table>
                <tr>
                    <th>Family ID</th>
                    <th>Address</th>
                </tr>';
        $i = 0;
        foreach ($sharedFamilies as $family) {
            $i++;
            echo '<tr>
                    <td>' . $family->id . '</td>
                    <td>' . $family->address->name . '</td>
                </tr>';

            if ($i == 2) {
                echo '<tr class="dark">
                    <td></td>
                    <td></td>
                </tr>';
                $i = 0;
            }
        }
        echo '</table>';
    }

    /**
     * If a user exist with the same email as a org member, link them togather so when they login they see schools they are associated to.
     */
    public function linkMembersWithUsers($school_id)
    {
        $families = OrgFamilies::where('organization_id', $school_id)->with('members')->get();

        foreach ($families as $members) {
            foreach ($members->members as $member) {
                if (isset($member->email) && !isset($member->user_id)) {
                    $user = User::where('email', $member->email)->first();
                    if (isset($user)) {
                        // dd($user);
                        // OrgMembers::where('email', $member->email)->update(['user_id' => $user->id]);
                        echo $user->email . '</br>';
                    }
                }
            }
        }
    }
}
