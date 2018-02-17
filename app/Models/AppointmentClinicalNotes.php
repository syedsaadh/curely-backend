<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentClinicalNotes extends Model
{
    protected $fillable = ['appointment_id', 'complaints', 'notes', 'observations', 'diagnosis'];
    protected $hidden = ['complaints', 'notes', 'observations', 'diagnosis', 'created_at', 'updated_at'];
    protected $appends = ['complaints_list', 'notes_list', 'observations_list', 'diagnosis_list'];
    public function getComplaintsListAttribute() {
        return !$this->complaints ? array() :explode(';', $this->complaints);
    }
    public function getDiagnosisListAttribute() {
        return !$this->diagnosis ? array() :explode(';', $this->diagnosis);
    }
    public function getObservationsListAttribute() {
        return !$this->observations ? array() : explode(';', $this->observations);
    }
    public function getNotesListAttribute() {
        return !$this->notes ? array() : explode(';', $this->notes);
    }
}
