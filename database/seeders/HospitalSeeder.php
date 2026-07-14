<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Specialization;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // SPECIALIZATIONS — full diagnostic lab / hospital unit catalog
        // ============================================================
        $specializations = [
            ['name' => 'Haematology', 'slug' => 'haematology', 'description' => 'Blood disorders, coagulation studies, and full blood count analysis.'],
            ['name' => 'Chemical Pathology', 'slug' => 'chemical-pathology', 'description' => 'Biochemical analysis of blood and body fluids for metabolic and organ function testing.'],
            ['name' => 'Medical Microbiology', 'slug' => 'medical-microbiology', 'description' => 'Culture and identification of bacteria, viruses, and fungi causing infection.'],
            ['name' => 'Histopathology', 'slug' => 'histopathology', 'description' => 'Tissue and cellular examination for disease diagnosis, including biopsies.'],
            ['name' => 'Immunology & Serology', 'slug' => 'immunology-serology', 'description' => 'Antibody, antigen, and infectious disease marker testing.'],
            ['name' => 'Blood Transfusion Science', 'slug' => 'blood-transfusion-science', 'description' => 'Blood grouping, cross-matching, and transfusion safety services.'],
            ['name' => 'Parasitology', 'slug' => 'parasitology', 'description' => 'Detection and identification of parasitic infections including malaria.'],
            ['name' => 'Virology', 'slug' => 'virology', 'description' => 'Detection and monitoring of viral infections.'],
            ['name' => 'Cytopathology', 'slug' => 'cytopathology', 'description' => 'Cellular-level screening for abnormalities, including cancer screening.'],
            ['name' => 'Molecular Diagnostics', 'slug' => 'molecular-diagnostics', 'description' => 'PCR-based and genetic testing for infectious and inherited conditions.'],
            ['name' => 'Endocrinology Testing', 'slug' => 'endocrinology-testing', 'description' => 'Hormonal assays for thyroid, reproductive, and metabolic disorders.'],
            ['name' => 'Toxicology', 'slug' => 'toxicology', 'description' => 'Detection of drugs, alcohol, and toxic substances in body fluids.'],
            ['name' => 'Radiology & Imaging', 'slug' => 'radiology-imaging', 'description' => 'X-ray, ultrasound, and other diagnostic imaging services.'],
            ['name' => 'Cardiology Diagnostics', 'slug' => 'cardiology-diagnostics', 'description' => 'ECG and cardiac function screening.'],
            ['name' => 'Reproductive Health & Fertility', 'slug' => 'reproductive-health-fertility', 'description' => 'Fertility hormone panels, semen analysis, and prenatal screening.'],
        ];

        $spec = collect();
        foreach ($specializations as $s) {
            $spec->put($s['slug'], Specialization::create($s));
        }

        // ============================================================
        // SERVICES — mapped to specializations by slug
        // ============================================================
        $services = [
            // Haematology
            ['name' => 'Full Blood Count (FBC)', 'slug' => 'full-blood-count', 'description' => 'Comprehensive blood count covering red cells, white cells, and platelets.', 'icon' => 'bi-droplet-half', 'price' => 5000, 'spec' => 'haematology'],
            ['name' => 'Coagulation Profile (PT/PTTK)', 'slug' => 'coagulation-profile', 'description' => 'Clotting function assessment.', 'icon' => 'bi-droplet', 'price' => 8500, 'spec' => 'haematology'],
            ['name' => 'Erythrocyte Sedimentation Rate (ESR)', 'slug' => 'esr-test', 'description' => 'Inflammation marker screening.', 'icon' => 'bi-droplet', 'price' => 2500, 'spec' => 'haematology'],
            ['name' => 'Sickling Test', 'slug' => 'sickling-test', 'description' => 'Screening for sickle cell trait/disease.', 'icon' => 'bi-droplet-half', 'price' => 3000, 'spec' => 'haematology'],

            // Chemical Pathology
            ['name' => 'Liver Function Test', 'slug' => 'liver-function-test', 'description' => 'Assessment of liver enzymes and function markers.', 'icon' => 'bi-clipboard2-pulse', 'price' => 7000, 'spec' => 'chemical-pathology'],
            ['name' => 'Kidney Function Test', 'slug' => 'kidney-function-test', 'description' => 'Urea, creatinine, and electrolyte panel.', 'icon' => 'bi-clipboard2-pulse', 'price' => 7000, 'spec' => 'chemical-pathology'],
            ['name' => 'Fasting Blood Sugar', 'slug' => 'fasting-blood-sugar', 'description' => 'Blood glucose screening for diabetes monitoring.', 'icon' => 'bi-capsule', 'price' => 3000, 'spec' => 'chemical-pathology'],
            ['name' => 'Lipid Profile', 'slug' => 'lipid-profile', 'description' => 'Cholesterol and triglyceride panel.', 'icon' => 'bi-clipboard2-pulse', 'price' => 6500, 'spec' => 'chemical-pathology'],
            ['name' => 'HbA1c (Glycated Haemoglobin)', 'slug' => 'hba1c-test', 'description' => '3-month average blood sugar control test.', 'icon' => 'bi-capsule', 'price' => 8000, 'spec' => 'chemical-pathology'],

            // Medical Microbiology
            ['name' => 'Stool & Urine Culture', 'slug' => 'stool-urine-culture', 'description' => 'Microbial culture and sensitivity testing.', 'icon' => 'bi-lungs', 'price' => 6000, 'spec' => 'medical-microbiology'],
            ['name' => 'Widal Test', 'slug' => 'widal-test', 'description' => 'Typhoid fever antibody screening.', 'icon' => 'bi-virus', 'price' => 3500, 'spec' => 'medical-microbiology'],
            ['name' => 'Wound Swab Culture', 'slug' => 'wound-swab-culture', 'description' => 'Identification of wound-infecting organisms.', 'icon' => 'bi-bandaid', 'price' => 6500, 'spec' => 'medical-microbiology'],
            ['name' => 'High Vaginal Swab (HVS)', 'slug' => 'high-vaginal-swab', 'description' => 'Screening for vaginal infections.', 'icon' => 'bi-lungs', 'price' => 5500, 'spec' => 'medical-microbiology'],

            // Histopathology
            ['name' => 'Histopathology Biopsy', 'slug' => 'histopathology-biopsy', 'description' => 'Tissue sample analysis for diagnosis of growths and lesions.', 'icon' => 'bi-heart-pulse', 'price' => 15000, 'spec' => 'histopathology'],
            ['name' => 'Fine Needle Aspiration Cytology (FNAC)', 'slug' => 'fnac-test', 'description' => 'Minimally invasive lump/mass sampling and analysis.', 'icon' => 'bi-heart-pulse', 'price' => 12000, 'spec' => 'histopathology'],

            // Immunology & Serology
            ['name' => 'HIV Screening', 'slug' => 'hiv-screening', 'description' => 'Confidential rapid antibody screening.', 'icon' => 'bi-shield-plus', 'price' => 4000, 'spec' => 'immunology-serology'],
            ['name' => 'Hepatitis B & C Screening', 'slug' => 'hepatitis-screening', 'description' => 'Screening for Hepatitis B surface antigen and Hepatitis C antibody.', 'icon' => 'bi-shield-plus', 'price' => 5500, 'spec' => 'immunology-serology'],
            ['name' => 'C-Reactive Protein (CRP)', 'slug' => 'crp-test', 'description' => 'Inflammation and infection marker.', 'icon' => 'bi-shield-plus', 'price' => 4500, 'spec' => 'immunology-serology'],
            ['name' => 'Rheumatoid Factor Test', 'slug' => 'rheumatoid-factor', 'description' => 'Screening for rheumatoid arthritis markers.', 'icon' => 'bi-shield-plus', 'price' => 5000, 'spec' => 'immunology-serology'],

            // Blood Transfusion Science
            ['name' => 'Blood Grouping & Genotype', 'slug' => 'blood-grouping-genotype', 'description' => 'ABO/Rh blood group and haemoglobin genotype testing.', 'icon' => 'bi-prescription2', 'price' => 4500, 'spec' => 'blood-transfusion-science'],
            ['name' => 'Cross-Matching', 'slug' => 'cross-matching', 'description' => 'Blood compatibility testing prior to transfusion.', 'icon' => 'bi-prescription2', 'price' => 6000, 'spec' => 'blood-transfusion-science'],

            // Parasitology
            ['name' => 'Malaria Parasite Test (MP)', 'slug' => 'malaria-parasite-test', 'description' => 'Microscopy-based malaria screening.', 'icon' => 'bi-bug', 'price' => 2500, 'spec' => 'parasitology'],
            ['name' => 'Stool Microscopy (Ova & Parasites)', 'slug' => 'stool-microscopy', 'description' => 'Detection of intestinal parasites and their eggs.', 'icon' => 'bi-bug', 'price' => 3500, 'spec' => 'parasitology'],

            // Virology
            ['name' => 'HIV Viral Load Test', 'slug' => 'hiv-viral-load', 'description' => 'Quantifies HIV virus levels for treatment monitoring.', 'icon' => 'bi-virus2', 'price' => 18000, 'spec' => 'virology'],
            ['name' => 'Hepatitis B Viral Load Test', 'slug' => 'hepatitis-b-viral-load', 'description' => 'Quantifies Hepatitis B virus levels.', 'icon' => 'bi-virus2', 'price' => 17000, 'spec' => 'virology'],

            // Cytopathology
            ['name' => 'Pap Smear Test', 'slug' => 'pap-smear-test', 'description' => 'Cervical cancer screening.', 'icon' => 'bi-gender-female', 'price' => 8000, 'spec' => 'cytopathology'],
            ['name' => 'Sputum Cytology', 'slug' => 'sputum-cytology', 'description' => 'Cellular examination of respiratory samples.', 'icon' => 'bi-lungs', 'price' => 7000, 'spec' => 'cytopathology'],

            // Molecular Diagnostics
            ['name' => 'COVID-19 PCR Test', 'slug' => 'covid-19-pcr-test', 'description' => 'Molecular detection of SARS-CoV-2.', 'icon' => 'bi-virus', 'price' => 25000, 'spec' => 'molecular-diagnostics'],
            ['name' => 'Tuberculosis GeneXpert Test', 'slug' => 'tb-genexpert-test', 'description' => 'Rapid molecular TB detection and drug-resistance screening.', 'icon' => 'bi-lungs', 'price' => 20000, 'spec' => 'molecular-diagnostics'],

            // Endocrinology Testing
            ['name' => 'Thyroid Function Test (TFT)', 'slug' => 'thyroid-function-test', 'description' => 'TSH, T3, and T4 hormone panel.', 'icon' => 'bi-capsule', 'price' => 12000, 'spec' => 'endocrinology-testing'],
            ['name' => 'Prolactin Test', 'slug' => 'prolactin-test', 'description' => 'Hormone screening relevant to fertility and pituitary function.', 'icon' => 'bi-capsule', 'price' => 9000, 'spec' => 'endocrinology-testing'],

            // Toxicology
            ['name' => 'Drug Screening Panel', 'slug' => 'drug-screening-panel', 'description' => 'Detection of common substances in urine.', 'icon' => 'bi-exclamation-triangle', 'price' => 15000, 'spec' => 'toxicology'],
            ['name' => 'Alcohol Level Test', 'slug' => 'alcohol-level-test', 'description' => 'Blood alcohol concentration testing.', 'icon' => 'bi-exclamation-triangle', 'price' => 6000, 'spec' => 'toxicology'],

            // Radiology & Imaging
            ['name' => 'Abdominal Ultrasound', 'slug' => 'abdominal-ultrasound', 'description' => 'Imaging of abdominal organs.', 'icon' => 'bi-camera', 'price' => 15000, 'spec' => 'radiology-imaging'],
            ['name' => 'Chest X-Ray', 'slug' => 'chest-x-ray', 'description' => 'Imaging of lungs and chest cavity.', 'icon' => 'bi-camera', 'price' => 8000, 'spec' => 'radiology-imaging'],
            ['name' => 'Obstetric Ultrasound (Pregnancy Scan)', 'slug' => 'obstetric-ultrasound', 'description' => 'Prenatal imaging and fetal monitoring.', 'icon' => 'bi-camera', 'price' => 12000, 'spec' => 'radiology-imaging'],

            // Cardiology Diagnostics
            ['name' => 'Electrocardiogram (ECG)', 'slug' => 'ecg-test', 'description' => 'Heart rhythm and electrical activity screening.', 'icon' => 'bi-heart-pulse', 'price' => 9000, 'spec' => 'cardiology-diagnostics'],
            ['name' => 'Cardiac Enzyme Panel', 'slug' => 'cardiac-enzyme-panel', 'description' => 'Blood markers for heart muscle damage.', 'icon' => 'bi-heart-pulse', 'price' => 14000, 'spec' => 'cardiology-diagnostics'],

            // Reproductive Health & Fertility
            ['name' => 'Semen Analysis', 'slug' => 'semen-analysis', 'description' => 'Fertility assessment for sperm count, motility, and morphology.', 'icon' => 'bi-gender-male', 'price' => 10000, 'spec' => 'reproductive-health-fertility'],
            ['name' => 'Hormonal Fertility Panel (FSH/LH/Estradiol)', 'slug' => 'hormonal-fertility-panel', 'description' => 'Comprehensive fertility hormone screening.', 'icon' => 'bi-gender-female', 'price' => 16000, 'spec' => 'reproductive-health-fertility'],
            ['name' => 'Beta hCG Pregnancy Test', 'slug' => 'beta-hcg-test', 'description' => 'Quantitative blood pregnancy confirmation.', 'icon' => 'bi-gender-female', 'price' => 4000, 'spec' => 'reproductive-health-fertility'],
        ];

        foreach ($services as $data) {
            $specSlug = $data['spec'];
            unset($data['spec']);
            $service = Service::create($data);
            $spec->get($specSlug)?->services()->attach($service->id);
        }
    }
}