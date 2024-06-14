<?php

namespace App\Http\Controllers;

use App\Models\OrgFamilies;
use App\Models\OrgMembers;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

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

    public function getNewData()
    {
        $filePath = public_path() . '/school-data.csv';

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        // Get the highest row number and column letter
        $highestRow = $worksheet->getHighestRow();
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

            $member = OrgMembers::where('email', $email)->first();

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
                continue;
            }
        }

        // Create a writer instance for CSV format
        $writer = new Csv($sheet2);

        // Set delimiter if needed (optional, default is comma)
        $writer->setDelimiter(',');

        // Save the file to the specified path
        $filename = 'new_data.csv';
        $writer->save($filename);

        echo "CSV file written to {$filename}";
    }

    public function updateData()
    {
        $filePath = public_path() . '/school-data.csv';

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        // Get the highest row number and column letter
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        if(isset($_GET['from'])) {
            $from = $_GET['from'];
        } else {
            $from = 2;
        }
        if(isset($_GET['till'])) {
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
                    $kid1 = OrgMembers::where('organization_family_id', $member->organization_family_id)
                        ->where('role', 2)
                        ->where('first_name', $student_1_fname)
                        ->where('last_name', $student_1_lname)
                        ->first();
                    if ($kid1) {
                        $kid1->update(['organization_grade_id' => $this->getGrade($student_1_grade)]);
                    } else {
                        OrgMembers::create([
                            'organization_family_id' => $member->organization_family_id,
                            'first_name' => $student_1_fname,
                            'last_name' => $student_1_lname,
                            'role' => 2,
                            'organization_grade_id' => $this->getGrade($student_1_grade),
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
                        $kid2->update(['organization_grade_id' => $this->getGrade($student_2_grade)]);
                    } else {
                        OrgMembers::create([
                            'organization_family_id' => $member->organization_family_id,
                            'first_name' => $student_2_fname,
                            'last_name' => $student_2_lname,
                            'role' => 2,
                            'organization_grade_id' => $this->getGrade($student_2_grade),
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
                        $kid3->update(['organization_grade_id' => $this->getGrade($student_3_grade)]);
                    } else {
                        OrgMembers::create([
                            'organization_family_id' => $member->organization_family_id,
                            'first_name' => $student_3_fname,
                            'last_name' => $student_3_lname,
                            'role' => 2,
                            'organization_grade_id' => $this->getGrade($student_3_grade),
                        ]);
                    }
                }
            }
            // dd($kid2);
            if($row == $till) {
                dd('50 done');
            }
        }
    }

    public function getGrade($grade)
    {
        if ($grade == 1) {
            return 76;
        } elseif ($grade == 2) {
            return 77;
        } elseif ($grade == 3) {
            return 78;
            // return 1185;
        } elseif ($grade == 4) {
            return 79;
            // return 1186;
        } elseif ($grade == 5) {
            return 80;
            // return 1187;
        } elseif ($grade == 6) {
            return 81;
            // return 1188;
        } elseif ($grade == 7) {
            return 82;
            // return 1189;
        } elseif ($grade == 8) {
            return 83;
            // return 1190;
        } elseif ($grade == 9) {
            return 84;
            // return 1191;
        } elseif ($grade == 10) {
            return 85;
            // return 1192;
        } elseif ($grade == 11) {
            return 86;
            // return 1193;
        } elseif ($grade == 12) {
            return 87;
            // return 1194;
        }
    }

    public function deleteGraduatedKids()
    {
        // find all kids with grade 12 and delete them. Make sure you do this before adding new data and updating old data.
        /**
         * Steps:
         * 1. Delete all kids in 12th grade as they graduated
         * 2. Update all kids grade
         * 3. Upload new kids/families
         */
    }
}
