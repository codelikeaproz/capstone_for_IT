{{-- Victim/Patient Inline Management --}}
<div class="mb-10" id="victims-management-section">
    <div class="flex items-center justify-between mb-1">
        <h2 class="text-lg font-semibold text-base-content">Victims/Patients Information</h2>
        <button
            type="button"
            onclick="addVictimForm()"
            class="btn btn-sm btn-primary gap-2"
        >
            <i class="fas fa-plus"></i>
            <span>Add Victim/Patient</span>
        </button>
    </div>
    <p class="text-sm text-base-content/60 mb-6">Add detailed information for each victim or patient involved in the incident</p>

    <!-- Victims Container -->
    <div id="victims-container" class="space-y-6">
        <!-- Victim forms will be added here dynamically -->
    </div>

    <!-- Empty State -->
    <div id="victims-empty-state" class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <div>
            <h3 class="font-bold">No Victims/Patients Added Yet</h3>
            <div class="text-xs">Click "Add Victim/Patient" button above to add victim information. You can add multiple victims and their medical details.</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ============================================
// VICTIM MANAGEMENT
// ============================================
let victimCount = 0;
let victims = [];

// Victim form template
function getVictimFormTemplate(index) {
    return `
        <div class="victim-form bg-base-200 rounded-lg p-6 relative" data-victim-index="${index}">
            <!-- Remove Button -->
            <button
                type="button"
                onclick="removeVictimForm(${index})"
                class="btn btn-circle btn-sm btn-error absolute top-4 right-4"
                title="Remove this victim"
            >
                <i class="fas fa-times"></i>
            </button>

            <h3 class="text-md font-semibold text-base-content mb-4">
                <i class="fas fa-user mr-2"></i>
                Victim/Patient #${index + 1}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Personal Information -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">First Name <span class="text-error">*</span></span>
                    </label>
                    <input
                        type="text"
                        name="victims[${index}][first_name]"
                        class="input input-bordered w-full focus:outline-primary"
                        placeholder="Enter first name"
                        required
                    >
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Last Name <span class="text-error">*</span></span>
                    </label>
                    <input
                        type="text"
                        name="victims[${index}][last_name]"
                        class="input input-bordered w-full focus:outline-primary"
                        placeholder="Enter last name"
                        required
                    >
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Age</span>
                    </label>
                    <input
                        type="number"
                        name="victims[${index}][age]"
                        class="input input-bordered w-full focus:outline-primary"
                        placeholder="Enter age"
                        min="0"
                        max="150"
                        onchange="checkAgeCategory(${index}, this.value)"
                    >
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Gender <span class="text-error">*</span></span>
                    </label>
                    <select
                        name="victims[${index}][gender]"
                        class="select select-bordered w-full focus:outline-primary"
                        onchange="togglePregnancyFields(${index}, this.value)"
                        required
                    >
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Contact Number</span>
                    </label>
                    <input
                        type="text"
                        name="victims[${index}][contact_number]"
                        class="input input-bordered w-full focus:outline-primary"
                        placeholder="e.g., 09XX-XXX-XXXX"
                    >
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">ID Number</span>
                    </label>
                    <input
                        type="text"
                        name="victims[${index}][id_number]"
                        class="input input-bordered w-full focus:outline-primary"
                        placeholder="Government ID"
                    >
                </div>

                <!-- Address -->
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text font-medium">Address</span>
                    </label>
                    <input
                        type="text"
                        name="victims[${index}][address]"
                        class="input input-bordered w-full focus:outline-primary"
                        placeholder="Complete address"
                    >
                </div>

                <!-- Medical Status -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Medical Status <span class="text-error">*</span></span>
                    </label>
                    <select
                        name="victims[${index}][medical_status]"
                        class="select select-bordered w-full focus:outline-primary"
                        onchange="toggleMedicalFields(${index}, this.value)"
                        required
                    >
                        <option value="">Select medical status</option>
                        <option value="uninjured">Uninjured</option>
                        <option value="minor_injury">Minor Injury</option>
                        <option value="major_injury">Major Injury</option>
                        <option value="critical">Critical</option>
                        <option value="deceased">Deceased</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Victim Role</span>
                    </label>
                    <select
                        name="victims[${index}][victim_role]"
                        class="select select-bordered w-full focus:outline-primary"
                    >
                        <option value="">Select role</option>
                        <option value="driver">Driver</option>
                        <option value="passenger">Passenger</option>
                        <option value="pedestrian">Pedestrian</option>
                        <option value="cyclist">Cyclist</option>
                        <option value="bystander">Bystander</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Injury Description -->
                <div class="form-control md:col-span-2" id="victim-${index}-injury-section" style="display: none;">
                    <label class="label">
                        <span class="label-text font-medium">Injury Description</span>
                    </label>
                    <textarea
                        name="victims[${index}][injury_description]"
                        rows="2"
                        class="textarea textarea-bordered w-full focus:outline-primary"
                        placeholder="Describe injuries or medical condition..."
                    ></textarea>
                </div>

                <!-- Pregnancy Section (Only for females) -->
                <div class="md:col-span-2" id="victim-${index}-pregnancy-section" style="display: none;">
                    <div class="divider">Pregnancy Information</div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3 bg-base-100 p-3 rounded-box">
                                <input
                                    type="checkbox"
                                    name="victims[${index}][is_pregnant]"
                                    id="victim-${index}-is-pregnant"
                                    value="1"
                                    class="checkbox checkbox-primary"
                                    onchange="togglePregnancyDetails(${index}, this.checked)"
                                >
                                <div>
                                    <span class="label-text font-medium block">Patient is Pregnant</span>
                                    <span class="text-xs text-base-content/60">Check if applicable</span>
                                </div>
                            </label>
                        </div>

                        <div id="victim-${index}-pregnancy-details" style="display: none;">
                            <div class="grid grid-cols-2 gap-2">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text text-xs">Trimester</span>
                                    </label>
                                    <select
                                        name="victims[${index}][pregnancy_trimester]"
                                        class="select select-bordered select-sm w-full"
                                    >
                                        <option value="">Select</option>
                                        <option value="first">First</option>
                                        <option value="second">Second</option>
                                        <option value="third">Third</option>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text text-xs">Due Date</span>
                                    </label>
                                    <input
                                        type="date"
                                        name="victims[${index}][expected_delivery_date]"
                                        class="input input-bordered input-sm w-full"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="form-control md:col-span-2" id="victim-${index}-pregnancy-complications" style="display: none;">
                            <label class="label">
                                <span class="label-text font-medium">Pregnancy Complications</span>
                            </label>
                            <textarea
                                name="victims[${index}][pregnancy_complications]"
                                rows="2"
                                class="textarea textarea-bordered w-full focus:outline-primary"
                                placeholder="Note any pregnancy-related complications or concerns..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Medical Vitals Section -->
                <div class="md:col-span-2" id="victim-${index}-vitals-section" style="display: none;">
                    <div class="divider">Medical Vitals</div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-xs">Blood Pressure</span>
                            </label>
                            <input
                                type="text"
                                name="victims[${index}][blood_pressure]"
                                class="input input-bordered input-sm w-full"
                                placeholder="120/80"
                            >
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-xs">Heart Rate</span>
                            </label>
                            <input
                                type="number"
                                name="victims[${index}][heart_rate]"
                                class="input input-bordered input-sm w-full"
                                placeholder="72"
                                min="0"
                                max="300"
                            >
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-xs">Temperature (°C)</span>
                            </label>
                            <input
                                type="number"
                                step="0.1"
                                name="victims[${index}][temperature]"
                                class="input input-bordered input-sm w-full"
                                placeholder="36.5"
                                min="30"
                                max="45"
                            >
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-xs">Respiratory Rate</span>
                            </label>
                            <input
                                type="number"
                                name="victims[${index}][respiratory_rate]"
                                class="input input-bordered input-sm w-full"
                                placeholder="16"
                                min="0"
                                max="100"
                            >
                        </div>

                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text text-xs">Consciousness Level</span>
                            </label>
                            <select
                                name="victims[${index}][consciousness_level]"
                                class="select select-bordered select-sm w-full"
                            >
                                <option value="">Select level</option>
                                <option value="alert">Alert (A)</option>
                                <option value="verbal">Responds to Verbal (V)</option>
                                <option value="pain">Responds to Pain (P)</option>
                                <option value="unresponsive">Unresponsive (U)</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-xs">Blood Type</span>
                            </label>
                            <input
                                type="text"
                                name="victims[${index}][blood_type]"
                                class="input input-bordered input-sm w-full"
                                placeholder="O+"
                            >
                        </div>
                    </div>
                </div>

                <!-- Hospital Information -->
                <div class="md:col-span-2" id="victim-${index}-hospital-section" style="display: none;">
                    <div class="divider">Hospital & Transportation</div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Hospital Referred</span>
                            </label>
                            <input
                                type="text"
                                name="victims[${index}][hospital_referred]"
                                class="input input-bordered w-full focus:outline-primary"
                                placeholder="Hospital name"
                            >
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Transportation Method</span>
                            </label>
                            <select
                                name="victims[${index}][transportation_method]"
                                class="select select-bordered w-full focus:outline-primary"
                            >
                                <option value="">Select method</option>
                                <option value="ambulance">Ambulance</option>
                                <option value="private_vehicle">Private Vehicle</option>
                                <option value="helicopter">Helicopter</option>
                                <option value="on_foot">On Foot</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Medical Treatment Given</span>
                            </label>
                            <textarea
                                name="victims[${index}][medical_treatment]"
                                rows="2"
                                class="textarea textarea-bordered w-full focus:outline-primary"
                                placeholder="Describe first aid or medical treatment provided..."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="md:col-span-2">
                    <div class="divider">Emergency Contact</div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Contact Name</span>
                            </label>
                            <input
                                type="text"
                                name="victims[${index}][emergency_contact_name]"
                                class="input input-bordered w-full focus:outline-primary"
                                placeholder="Full name"
                            >
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Contact Phone</span>
                            </label>
                            <input
                                type="text"
                                name="victims[${index}][emergency_contact_phone]"
                                class="input input-bordered w-full focus:outline-primary"
                                placeholder="Phone number"
                            >
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Relationship</span>
                            </label>
                            <input
                                type="text"
                                name="victims[${index}][emergency_contact_relationship]"
                                class="input input-bordered w-full focus:outline-primary"
                                placeholder="e.g., Spouse, Parent"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Add victim form
function addVictimForm() {
    const container = document.getElementById('victims-container');
    const emptyState = document.getElementById('victims-empty-state');

    if (emptyState) {
        emptyState.style.display = 'none';
    }

    const victimForm = document.createElement('div');
    victimForm.innerHTML = getVictimFormTemplate(victimCount);
    container.appendChild(victimForm);

    victims.push({ index: victimCount });
    victimCount++;

    showSuccessToast('Victim form added');
}

// Remove victim form
function removeVictimForm(index) {
    if (confirm('Are you sure you want to remove this victim?')) {
        const victimForm = document.querySelector(`[data-victim-index="${index}"]`);
        if (victimForm) {
            victimForm.remove();
        }

        victims = victims.filter(v => v.index !== index);

        // Show empty state if no victims
        if (victims.length === 0) {
            const emptyState = document.getElementById('victims-empty-state');
            if (emptyState) {
                emptyState.style.display = 'flex';
            }
        }

        showSuccessToast('Victim removed');
    }
}

// Toggle pregnancy fields based on gender
function togglePregnancyFields(index, gender) {
    const pregnancySection = document.getElementById(`victim-${index}-pregnancy-section`);

    if (pregnancySection) {
        pregnancySection.style.display = gender === 'female' ? 'block' : 'none';
    }
}

// Toggle pregnancy details
function togglePregnancyDetails(index, isPregnant) {
    const detailsSection = document.getElementById(`victim-${index}-pregnancy-details`);
    const complicationsSection = document.getElementById(`victim-${index}-pregnancy-complications`);

    if (detailsSection) {
        detailsSection.style.display = isPregnant ? 'block' : 'none';
    }

    if (complicationsSection) {
        complicationsSection.style.display = isPregnant ? 'block' : 'none';
    }
}

// Toggle medical fields based on medical status
function toggleMedicalFields(index, medicalStatus) {
    const injurySection = document.getElementById(`victim-${index}-injury-section`);
    const vitalsSection = document.getElementById(`victim-${index}-vitals-section`);
    const hospitalSection = document.getElementById(`victim-${index}-hospital-section`);

    // Show injury description for injured statuses
    const showInjury = ['minor_injury', 'major_injury', 'critical', 'deceased'].includes(medicalStatus);
    if (injurySection) {
        injurySection.style.display = showInjury ? 'block' : 'none';
    }

    // Show vitals for critical injuries
    const showVitals = ['major_injury', 'critical'].includes(medicalStatus);
    if (vitalsSection) {
        vitalsSection.style.display = showVitals ? 'block' : 'none';
    }

    // Show hospital info for injuries
    const showHospital = ['minor_injury', 'major_injury', 'critical'].includes(medicalStatus);
    if (hospitalSection) {
        hospitalSection.style.display = showHospital ? 'block' : 'none';
    }
}

// Check age category and show alert for special care
function checkAgeCategory(index, age) {
    if (age < 13 || age >= 60) {
        const category = age < 13 ? 'child' : 'elderly';
        showInfoToast(`This ${category} may require special care attention.`);
    }
}

// Info toast
function showInfoToast(message) {
    console.log('Info:', message);
    // You can implement actual toast here
}
</script>
@endpush

