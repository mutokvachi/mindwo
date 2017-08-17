<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduUniqueIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('edu_subjects_groups_attend', function (Blueprint $table) {
            $table->unique(['group_day_id', 'student_id']);
        });
        
        Schema::table('edu_subjects_groups_members', function (Blueprint $table) {
            $table->unique(['group_id', 'student_id']);
        });
       
        Schema::table('edu_subjects_groups_days_teachers', function (Blueprint $table) {
            $table->unique(['group_day_id', 'teacher_id', 'time_from', 'time_to'], 'edu_subjects_groups_days_teachers_uniq');
        });
        
        Schema::table('edu_subjects_tags', function (Blueprint $table) {
            $table->unique(['subject_id', 'tag_id']);
        });
        
        Schema::table('edu_subjects_materials', function (Blueprint $table) {
            $table->unique(['subject_id', 'material_id']);
        });
        
        Schema::table('edu_orgs_banks', function (Blueprint $table) {
            $table->unique(['org_id', 'bank_id', 'account']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('edu_subjects_groups_attend', function (Blueprint $table) {
            $table->dropUnique('edu_subjects_groups_attend_group_day_id_student_id_unique');
        });
        
        Schema::table('edu_subjects_groups_members', function (Blueprint $table) {
            $table->dropUnique('edu_subjects_groups_members_group_id_student_id_unique');
        });
        
        Schema::table('edu_subjects_groups_days_teachers', function (Blueprint $table) {
            $table->dropUnique('edu_subjects_groups_days_teachers_uniq');
        });
        
        Schema::table('edu_subjects_tags', function (Blueprint $table) {
            $table->dropUnique('edu_subjects_tags_subject_id_tag_id_unique');
        });
        
        Schema::table('edu_subjects_materials', function (Blueprint $table) {
            $table->dropUnique('edu_subjects_materials_subject_id_material_id_unique');
        });
        
        Schema::table('edu_orgs_banks', function (Blueprint $table) {
            $table->dropUnique('edu_orgs_banks_org_id_bank_id_account_unique');
        });
    }
}
