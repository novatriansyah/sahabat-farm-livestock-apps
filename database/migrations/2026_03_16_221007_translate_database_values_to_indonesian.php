<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. users: role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER', 'BREEDER', 'STAFF', 'PARTNER', 'PEMILIK', 'PETERNAK', 'STAF', 'MITRA') DEFAULT 'STAF'");
        DB::table('users')->where('role', 'OWNER')->update(['role' => 'PEMILIK']);
        DB::table('users')->where('role', 'BREEDER')->update(['role' => 'PETERNAK']);
        DB::table('users')->where('role', 'STAFF')->update(['role' => 'STAF']);
        DB::table('users')->where('role', 'PARTNER')->update(['role' => 'MITRA']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('PEMILIK', 'PETERNAK', 'STAF', 'MITRA') DEFAULT 'STAF'");

        // 2. animals: gender
        DB::statement("ALTER TABLE animals MODIFY COLUMN gender ENUM('MALE', 'FEMALE', 'JANTAN', 'BETINA')");
        DB::table('animals')->where('gender', 'MALE')->update(['gender' => 'JANTAN']);
        DB::table('animals')->where('gender', 'FEMALE')->update(['gender' => 'BETINA']);
        DB::statement("ALTER TABLE animals MODIFY COLUMN gender ENUM('JANTAN', 'BETINA')");

        // 3. animals: acquisition_type
        DB::statement("ALTER TABLE animals MODIFY COLUMN acquisition_type ENUM('BRED', 'BOUGHT', 'HASIL_TERNAK', 'BELI')");
        DB::table('animals')->where('acquisition_type', 'BRED')->update(['acquisition_type' => 'HASIL_TERNAK']);
        DB::table('animals')->where('acquisition_type', 'BOUGHT')->update(['acquisition_type' => 'BELI']);
        DB::statement("ALTER TABLE animals MODIFY COLUMN acquisition_type ENUM('HASIL_TERNAK', 'BELI')");

        // 4. animals: health_status
        DB::statement("ALTER TABLE animals MODIFY COLUMN health_status ENUM('HEALTHY', 'SICK', 'QUARANTINE', 'DECEASED', 'SOLD', 'SEHAT', 'SAKIT', 'KARANTINA', 'MATI', 'TERJUAL') DEFAULT 'SEHAT'");
        DB::table('animals')->where('health_status', 'HEALTHY')->update(['health_status' => 'SEHAT']);
        DB::table('animals')->where('health_status', 'SICK')->update(['health_status' => 'SAKIT']);
        DB::table('animals')->where('health_status', 'QUARANTINE')->update(['health_status' => 'KARANTINA']);
        DB::table('animals')->where('health_status', 'DECEASED')->update(['health_status' => 'MATI']);
        DB::table('animals')->where('health_status', 'SOLD')->update(['health_status' => 'TERJUAL']);
        DB::statement("ALTER TABLE animals MODIFY COLUMN health_status ENUM('SEHAT', 'SAKIT', 'KARANTINA', 'MATI', 'TERJUAL') DEFAULT 'SEHAT'");

        // 5. exit_logs: exit_type
        DB::statement("ALTER TABLE exit_logs MODIFY COLUMN exit_type ENUM('DEATH', 'SALE', 'MATI', 'JUAL')");
        DB::table('exit_logs')->where('exit_type', 'DEATH')->update(['exit_type' => 'MATI']);
        DB::table('exit_logs')->where('exit_type', 'SALE')->update(['exit_type' => 'JUAL']);
        DB::statement("ALTER TABLE exit_logs MODIFY COLUMN exit_type ENUM('MATI', 'JUAL')");

        // 6. breeding_events: status
        DB::statement("ALTER TABLE breeding_events MODIFY COLUMN status ENUM('PENDING', 'SUCCESS', 'FAILED', 'COMPLETED', 'MENUNGGU', 'BERHASIL', 'GAGAL', 'SELESAI') DEFAULT 'MENUNGGU'");
        DB::table('breeding_events')->where('status', 'PENDING')->update(['status' => 'MENUNGGU']);
        DB::table('breeding_events')->where('status', 'SUCCESS')->update(['status' => 'BERHASIL']);
        DB::table('breeding_events')->where('status', 'FAILED')->update(['status' => 'GAGAL']);
        DB::table('breeding_events')->where('status', 'COMPLETED')->update(['status' => 'SELESAI']);
        DB::statement("ALTER TABLE breeding_events MODIFY COLUMN status ENUM('MENUNGGU', 'BERHASIL', 'GAGAL', 'SELESAI') DEFAULT 'MENUNGGU'");

        // 7. animal_tasks: status
        DB::statement("ALTER TABLE animal_tasks MODIFY COLUMN status ENUM('PENDING', 'COMPLETED', 'MENUNGGU', 'SELESAI') DEFAULT 'MENUNGGU'");
        DB::table('animal_tasks')->where('status', 'PENDING')->update(['status' => 'MENUNGGU']);
        DB::table('animal_tasks')->where('status', 'COMPLETED')->update(['status' => 'SELESAI']);
        DB::statement("ALTER TABLE animal_tasks MODIFY COLUMN status ENUM('MENUNGGU', 'SELESAI') DEFAULT 'MENUNGGU'");

        // 8. invoices: status
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('DRAFT', 'ISSUED', 'PAID', 'CANCELLED', 'DITERBITKAN', 'LUNAS', 'DIBATALKAN') DEFAULT 'DRAFT'");
        DB::table('invoices')->where('status', 'ISSUED')->update(['status' => 'DITERBITKAN']);
        DB::table('invoices')->where('status', 'PAID')->update(['status' => 'LUNAS']);
        DB::table('invoices')->where('status', 'CANCELLED')->update(['status' => 'DIBATALKAN']);
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('DRAFT', 'DITERBITKAN', 'LUNAS', 'DIBATALKAN') DEFAULT 'DRAFT'");

        // 9. invoices: type
        DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('PROFORMA', 'COMMERCIAL', 'KOMERSIAL') DEFAULT 'PROFORMA'");
        DB::table('invoices')->where('type', 'COMMERCIAL')->update(['type' => 'KOMERSIAL']);
        DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('PROFORMA', 'KOMERSIAL') DEFAULT 'PROFORMA'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. users: role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('PEMILIK', 'PETERNAK', 'STAF', 'MITRA', 'OWNER', 'BREEDER', 'STAFF', 'PARTNER') DEFAULT 'STAFF'");
        DB::table('users')->where('role', 'PEMILIK')->update(['role' => 'OWNER']);
        DB::table('users')->where('role', 'PETERNAK')->update(['role' => 'BREEDER']);
        DB::table('users')->where('role', 'STAF')->update(['role' => 'STAFF']);
        DB::table('users')->where('role', 'MITRA')->update(['role' => 'PARTNER']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER', 'BREEDER', 'STAFF', 'PARTNER') DEFAULT 'STAFF'");

        // 2. animals: gender
        DB::statement("ALTER TABLE animals MODIFY COLUMN gender ENUM('JANTAN', 'BETINA', 'MALE', 'FEMALE')");
        DB::table('animals')->where('gender', 'JANTAN')->update(['gender' => 'MALE']);
        DB::table('animals')->where('gender', 'BETINA')->update(['gender' => 'FEMALE']);
        DB::statement("ALTER TABLE animals MODIFY COLUMN gender ENUM('MALE', 'FEMALE')");

        // 3. animals: acquisition_type
        DB::statement("ALTER TABLE animals MODIFY COLUMN acquisition_type ENUM('HASIL_TERNAK', 'BELI', 'BRED', 'BOUGHT')");
        DB::table('animals')->where('acquisition_type', 'HASIL_TERNAK')->update(['acquisition_type' => 'BRED']);
        DB::table('animals')->where('acquisition_type', 'BELI')->update(['acquisition_type' => 'BOUGHT']);
        DB::statement("ALTER TABLE animals MODIFY COLUMN acquisition_type ENUM('BRED', 'BOUGHT')");

        // 4. animals: health_status
        DB::statement("ALTER TABLE animals MODIFY COLUMN health_status ENUM('SEHAT', 'SAKIT', 'KARANTINA', 'MATI', 'TERJUAL', 'HEALTHY', 'SICK', 'QUARANTINE', 'DECEASED', 'SOLD') DEFAULT 'HEALTHY'");
        DB::table('animals')->where('health_status', 'SEHAT')->update(['health_status' => 'HEALTHY']);
        DB::table('animals')->where('health_status', 'SAKIT')->update(['health_status' => 'SICK']);
        DB::table('animals')->where('health_status', 'KARANTINA')->update(['health_status' => 'QUARANTINE']);
        DB::table('animals')->where('health_status', 'MATI')->update(['health_status' => 'DECEASED']);
        DB::table('animals')->where('health_status', 'TERJUAL')->update(['health_status' => 'SOLD']);
        DB::statement("ALTER TABLE animals MODIFY COLUMN health_status ENUM('HEALTHY', 'SICK', 'QUARANTINE', 'DECEASED', 'SOLD') DEFAULT 'HEALTHY'");

        // 5. exit_logs: exit_type
        DB::statement("ALTER TABLE exit_logs MODIFY COLUMN exit_type ENUM('MATI', 'JUAL', 'DEATH', 'SALE')");
        DB::table('exit_logs')->where('exit_type', 'MATI')->update(['exit_type' => 'DEATH']);
        DB::table('exit_logs')->where('exit_type', 'JUAL')->update(['exit_type' => 'SALE']);
        DB::statement("ALTER TABLE exit_logs MODIFY COLUMN exit_type ENUM('DEATH', 'SALE')");

        // 6. breeding_events: status
        DB::statement("ALTER TABLE breeding_events MODIFY COLUMN status ENUM('MENUNGGU', 'BERHASIL', 'GAGAL', 'SELESAI', 'PENDING', 'SUCCESS', 'FAILED', 'COMPLETED') DEFAULT 'PENDING'");
        DB::table('breeding_events')->where('status', 'MENUNGGU')->update(['status' => 'PENDING']);
        DB::table('breeding_events')->where('status', 'BERHASIL')->update(['status' => 'SUCCESS']);
        DB::table('breeding_events')->where('status', 'GAGAL')->update(['status' => 'FAILED']);
        DB::table('breeding_events')->where('status', 'SELESAI')->update(['status' => 'COMPLETED']);
        DB::statement("ALTER TABLE breeding_events MODIFY COLUMN status ENUM('PENDING', 'SUCCESS', 'FAILED', 'COMPLETED') DEFAULT 'PENDING'");

        // 7. animal_tasks: status
        DB::statement("ALTER TABLE animal_tasks MODIFY COLUMN status ENUM('MENUNGGU', 'SELESAI', 'PENDING', 'COMPLETED') DEFAULT 'PENDING'");
        DB::table('animal_tasks')->where('status', 'MENUNGGU')->update(['status' => 'PENDING']);
        DB::table('animal_tasks')->where('status', 'SELESAI')->update(['status' => 'COMPLETED']);
        DB::statement("ALTER TABLE animal_tasks MODIFY COLUMN status ENUM('PENDING', 'COMPLETED') DEFAULT 'PENDING'");

        // 8. invoices: status
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('DRAFT', 'DITERBITKAN', 'LUNAS', 'DIBATALKAN', 'ISSUED', 'PAID', 'CANCELLED') DEFAULT 'DRAFT'");
        DB::table('invoices')->where('status', 'DITERBITKAN')->update(['status' => 'ISSUED']);
        DB::table('invoices')->where('status', 'LUNAS')->update(['status' => 'PAID']);
        DB::table('invoices')->where('status', 'DIBATALKAN')->update(['status' => 'CANCELLED']);
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('DRAFT', 'ISSUED', 'PAID', 'CANCELLED') DEFAULT 'DRAFT'");

        // 9. invoices: type
        DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('PROFORMA', 'KOMERSIAL', 'COMMERCIAL') DEFAULT 'PROFORMA'");
        DB::table('invoices')->where('type', 'KOMERSIAL')->update(['type' => 'COMMERCIAL']);
        DB::statement("ALTER TABLE invoices MODIFY COLUMN type ENUM('PROFORMA', 'COMMERCIAL') DEFAULT 'PROFORMA'");
    }
};
