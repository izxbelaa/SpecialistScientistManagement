// submission_period.js

class SubmissionPeriodManager {
    constructor(formId) {
      this.form = document.getElementById(formId);
      this.startInput = this.form.querySelector("#startDate");
      this.endInput = this.form.querySelector("#endDate");
      this.currentSettingsSpan = this.form.querySelector("#currentSettings");
      this.cancelBtn = this.form.querySelector("#cancelBtn");
  
      this.bindEvents();
      this.loadCurrentSettings();
    }
  
    // Bind event listeners to form and buttons.
    bindEvents() {
      this.form.addEventListener('submit', this.handleSubmit.bind(this));
      this.cancelBtn.addEventListener('click', this.handleCancel.bind(this));
    }
  
    // Load current submission period settings.
    loadCurrentSettings() {
      // In a real application, fetch these values from your backend.
      const currentStart = "2025-04-10T09:00";
      const currentEnd = "2025-04-15T17:00";
      this.currentSettingsSpan.textContent = `Current Submission Period: ${currentStart} to ${currentEnd}`;
      
      // Pre-fill the inputs for convenience.
      this.startInput.value = currentStart;
      this.endInput.value = currentEnd;
    }
  
    // Handle form submission.
    handleSubmit(event) {
      event.preventDefault();
      const startValue = this.startInput.value;
      const endValue = this.endInput.value;
      
      if (new Date(startValue) >= new Date(endValue)) {
        alert("End Date/Time must be after Start Date/Time.");
        return;
      }
      
      // Simulate saving the settings.
      console.log("Saving Submission Period:", startValue, endValue);
      
      // You can use fetch() to send data to the server here.
      // Example:
      // fetch('api/saveSubmissionPeriod', {
      //   method: 'POST',
      //   headers: { 'Content-Type': 'application/json' },
      //   body: JSON.stringify({ start: startValue, end: endValue })
      // })
      // .then(response => response.json())
      // .then(data => console.log(data));
  
      alert("Submission period updated successfully.");
    }
  
    // Handle the cancel button click.
    handleCancel() {
      this.form.reset();
      this.loadCurrentSettings();
    }
  }
  
  // Initialize the SubmissionPeriodManager when the page loads.
  document.addEventListener('DOMContentLoaded', () => {
    new SubmissionPeriodManager("submissionForm");
  });
  