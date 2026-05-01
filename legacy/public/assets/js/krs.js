/**
 * SIAKAD Gallery — KRS (Kartu Rencana Studi) Page JavaScript
 * Handles KRS enrollment validation, schedule conflict detection, and real-time updates
 */

// ============================================================================
// KRS State Management
// ============================================================================

const KRSState = {
  selectedCourses: [],
  totalSKS: 0,
  maxSKS: 0,
  courses: [],
  schedules: {}, // courseId -> {day, jamMulai, jamSelesai, ruang}

  addCourse: function (course) {
    if (!this.selectedCourses.find((c) => c.id === course.id)) {
      this.selectedCourses.push(course);
      this.totalSKS += parseInt(course.sks) || 0;
    }
  },

  removeCourse: function (courseId) {
    const course = this.selectedCourses.find((c) => c.id === courseId);
    if (course) {
      this.totalSKS -= parseInt(course.sks) || 0;
      this.selectedCourses = this.selectedCourses.filter((c) => c.id !== courseId);
    }
  },

  hasCourse: function (courseId) {
    return this.selectedCourses.some((c) => c.id === courseId);
  },

  getConflictingCourses: function (courseId) {
    const course = this.courses.find((c) => c.id === courseId);
    if (!course || !this.schedules[courseId]) return [];

    const courseSchedule = this.schedules[courseId];
    const conflicts = [];

    this.selectedCourses.forEach((selected) => {
      if (selected.id === courseId || !this.schedules[selected.id]) return;

      const selectedSchedule = this.schedules[selected.id];

      // Check if same day and overlapping time
      if (courseSchedule.day === selectedSchedule.day) {
        if (this.timesOverlap(courseSchedule.jamMulai, courseSchedule.jamSelesai, selectedSchedule.jamMulai, selectedSchedule.jamSelesai)) {
          conflicts.push(selected);
        }
      }
    });

    return conflicts;
  },

  timesOverlap: function (start1, end1, start2, end2) {
    // Convert time strings (HH:MM) to minutes for comparison
    const toMinutes = (time) => {
      const [h, m] = time.split(':').map(Number);
      return h * 60 + m;
    };

    const start1Min = toMinutes(start1);
    const end1Min = toMinutes(end1);
    const start2Min = toMinutes(start2);
    const end2Min = toMinutes(end2);

    return start1Min < end2Min && start2Min < end1Min;
  },

  reset: function () {
    this.selectedCourses = [];
    this.totalSKS = 0;
  },
};

// ============================================================================
// DOM Ready & Initialization
// ============================================================================

document.addEventListener('DOMContentLoaded', function () {
  initKRSPage();
});

function initKRSPage() {
  // Get max SKS from page data
  const maxSksElement = document.querySelector('[data-max-sks]');
  if (maxSksElement) {
    KRSState.maxSKS = parseInt(maxSksElement.getAttribute('data-max-sks')) || 0;
  }

  // Initialize course list
  initCourseList();

  // Initialize event listeners
  initCheckboxListeners();
  initFilterListeners();
  initButtonListeners();

  // Initial update
  updateKRSProgress();
}

// ============================================================================
// Course List Initialization
// ============================================================================

function initCourseList() {
  const courseRows = document.querySelectorAll('[data-course-id]');

  courseRows.forEach((row) => {
    const courseId = row.getAttribute('data-course-id');
    const sks = row.getAttribute('data-sks');
    const day = row.getAttribute('data-day');
    const jamMulai = row.getAttribute('data-jam-mulai');
    const jamSelesai = row.getAttribute('data-jam-selesai');

    const course = {
      id: courseId,
      sks: sks,
      element: row,
    };

    KRSState.courses.push(course);
    KRSState.schedules[courseId] = {
      day: day,
      jamMulai: jamMulai,
      jamSelesai: jamSelesai,
    };
  });
}

// ============================================================================
// Checkbox Event Listeners
// ============================================================================

function initCheckboxListeners() {
  const checkboxes = document.querySelectorAll('[data-course-checkbox]');

  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', function () {
      const courseId = this.getAttribute('data-course-checkbox');
      const courseRow = document.querySelector(`[data-course-id="${courseId}"]`);
      const sksValue = parseInt(courseRow.getAttribute('data-sks')) || 0;

      if (this.checked) {
        // Add course
        const course = KRSState.courses.find((c) => c.id === courseId);
        if (course) {
          KRSState.addCourse(course);
          courseRow.classList.add('selected');

          // Check for conflicts
          const conflicts = KRSState.getConflictingCourses(courseId);
          if (conflicts.length > 0) {
            showConflictWarning(courseId, conflicts);
          }
        }
      } else {
        // Remove course
        KRSState.removeCourse(courseId);
        courseRow.classList.remove('selected');
        clearConflictWarning(courseId);
      }

      // Check max SKS
      if (KRSState.totalSKS > KRSState.maxSKS) {
        showMaxSKSWarning();
        this.checked = false;
        KRSState.removeCourse(courseId);
        courseRow.classList.remove('selected');
      } else {
        clearMaxSKSWarning();
      }

      updateKRSProgress();
    });
  });
}

// ============================================================================
// Filter Listeners
// ============================================================================

function initFilterListeners() {
  const filterBtns = document.querySelectorAll('[data-krs-filter]');

  filterBtns.forEach((btn) => {
    btn.addEventListener('click', function () {
      const filterType = this.getAttribute('data-krs-filter');

      // Update active state
      filterBtns.forEach((b) => b.classList.remove('active'));
      this.classList.add('active');

      // Filter course rows
      filterCourses(filterType);
    });
  });
}

function filterCourses(filterType) {
  const courseRows = document.querySelectorAll('[data-course-id]');

  courseRows.forEach((row) => {
    const courseType = row.getAttribute('data-course-type');

    if (filterType === 'all') {
      row.style.display = '';
    } else if (filterType === courseType) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

// ============================================================================
// Button Event Listeners
// ============================================================================

function initButtonListeners() {
  const resetBtn = document.querySelector('[data-krs-reset]');
  const saveBtn = document.querySelector('[data-krs-save]');

  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      if (confirm('Apakah Anda yakin ingin menghapus semua pilihan?')) {
        resetKRSSelection();
      }
    });
  }

  if (saveBtn) {
    saveBtn.addEventListener('click', function () {
      saveKRS();
    });
  }
}

// ============================================================================
// Update KRS Progress Bar & Info
// ============================================================================

function updateKRSProgress() {
  const progressText = document.querySelector('[data-krs-progress-text]');
  const progressFill = document.querySelector('[data-krs-progress-fill]');
  const selectedCountText = document.querySelector('[data-krs-selected-count]');
  const mandatoryCount = document.querySelector('[data-krs-mandatory-count]');
  const electiveCount = document.querySelector('[data-krs-elective-count]');

  if (progressText) {
    progressText.textContent = KRSState.totalSKS + '/' + KRSState.maxSKS + ' SKS DIPILIH';
  }

  if (progressFill) {
    const percentage = (KRSState.totalSKS / KRSState.maxSKS) * 100;
    progressFill.style.width = Math.min(percentage, 100) + '%';
  }

  if (selectedCountText) {
    selectedCountText.textContent = KRSState.selectedCourses.length + ' MATAKULIAH';
  }

  // Count mandatory and elective
  let mandatoryTotal = 0;
  let electiveTotal = 0;

  KRSState.selectedCourses.forEach((course) => {
    const courseRow = document.querySelector(`[data-course-id="${course.id}"]`);
    if (courseRow) {
      const courseType = courseRow.getAttribute('data-course-type');
      const sks = parseInt(courseRow.getAttribute('data-sks')) || 0;

      if (courseType === 'wajib') {
        mandatoryTotal += sks;
      } else if (courseType === 'pilihan') {
        electiveTotal += sks;
      }
    }
  });

  if (mandatoryCount) {
    mandatoryCount.textContent = mandatoryTotal;
  }
  if (electiveCount) {
    electiveCount.textContent = electiveTotal;
  }
}

// ============================================================================
// Conflict Detection & Warnings
// ============================================================================

function showConflictWarning(courseId, conflicts) {
  const courseRow = document.querySelector(`[data-course-id="${courseId}"]`);
  if (!courseRow) return;

  // Remove existing warning if any
  clearConflictWarning(courseId);

  // Create warning message
  const conflictCodes = conflicts.map((c) => {
    const row = document.querySelector(`[data-course-id="${c.id}"]`);
    return row ? row.getAttribute('data-course-code') : '';
  });

  const warningDiv = document.createElement('div');
  warningDiv.className = 'alert alert-warning mt-md';
  warningDiv.setAttribute('data-conflict-warning', courseId);
  warningDiv.innerHTML = `<strong>Perhatian:</strong> Jadwal berbenturan dengan ${conflictCodes.join(', ')}`;

  courseRow.after(warningDiv);
}

function clearConflictWarning(courseId) {
  const warning = document.querySelector(`[data-conflict-warning="${courseId}"]`);
  if (warning) {
    warning.remove();
  }
}

function showMaxSKSWarning() {
  let alertBox = document.querySelector('[data-max-sks-warning]');

  if (!alertBox) {
    alertBox = document.createElement('div');
    alertBox.className = 'alert alert-warning';
    alertBox.setAttribute('data-max-sks-warning', '');
    alertBox.innerHTML = `<strong>Batas Maksimal Terlampaui:</strong> Total SKS Anda sudah mencapai batas maksimal (${KRSState.maxSKS} SKS).`;

    const container = document.querySelector('[data-krs-alerts]');
    if (container) {
      container.appendChild(alertBox);
    } else {
      document.querySelector('[data-course-id]').parentElement.insertBefore(alertBox, document.querySelector('[data-course-id]'));
    }
  }
}

function clearMaxSKSWarning() {
  const alertBox = document.querySelector('[data-max-sks-warning]');
  if (alertBox && KRSState.totalSKS <= KRSState.maxSKS) {
    alertBox.remove();
  }
}

// ============================================================================
// Reset Selection
// ============================================================================

function resetKRSSelection() {
  const checkboxes = document.querySelectorAll('[data-course-checkbox]');
  checkboxes.forEach((checkbox) => {
    checkbox.checked = false;
  });

  const courseRows = document.querySelectorAll('[data-course-id]');
  courseRows.forEach((row) => {
    row.classList.remove('selected');
  });

  // Clear conflict warnings
  const warnings = document.querySelectorAll('[data-conflict-warning]');
  warnings.forEach((w) => w.remove());

  KRSState.reset();
  updateKRSProgress();
  clearMaxSKSWarning();
}

// ============================================================================
// Save KRS
// ============================================================================

function saveKRS() {
  // Validate selection
  if (KRSState.selectedCourses.length === 0) {
    window.SIAKAD.showNotification('Silakan pilih minimal 1 matakuliah', 'warning');
    return;
  }

  if (KRSState.totalSKS > KRSState.maxSKS) {
    window.SIAKAD.showNotification('Total SKS melebihi batas maksimal', 'error');
    return;
  }

  // Prepare data
  const jadwalIds = KRSState.selectedCourses.map((c) => c.id);

  // Show loading state
  const saveBtn = document.querySelector('[data-krs-save]');
  const originalText = saveBtn.textContent;
  saveBtn.disabled = true;
  saveBtn.textContent = 'Menyimpan...';

  // Send AJAX request
  window.SIAKAD.ajaxRequest(window.SIAKAD.getAppUrl() + '/api/krs_save.php', {
    method: 'POST',
    body: {
      jadwal_ids: jadwalIds,
      _csrf: window.SIAKAD.getCSRFToken(),
    },
  })
    .then((response) => {
      if (response.ok) {
        window.SIAKAD.showNotification('KRS berhasil disimpan!', 'success');
        // Redirect after 2 seconds
        setTimeout(() => {
          window.location.href = '/web-krs-mahasiswa/public/mahasiswa/khs.php';
        }, 2000);
      } else {
        window.SIAKAD.showNotification('Gagal menyimpan KRS: ' + (response.message || ''), 'error');
        console.error('Validation errors:', response.errors);

        // Highlight error rows
        if (response.errors && Array.isArray(response.errors)) {
          response.errors.forEach((error) => {
            console.error('Error:', error);
          });
        }
      }
    })
    .catch((error) => {
      window.SIAKAD.showNotification('Terjadi kesalahan saat menyimpan', 'error');
      console.error('Save error:', error);
    })
    .finally(() => {
      saveBtn.disabled = false;
      saveBtn.textContent = originalText;
    });
}

// ============================================================================
// Export for use in other modules
// ============================================================================

window.KRS = {
  state: KRSState,
  resetSelection: resetKRSSelection,
  saveKRS: saveKRS,
};
