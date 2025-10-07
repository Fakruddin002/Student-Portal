import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-complaint',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],  // ✅ Add RouterModule for navigation
  templateUrl: './complaint.html',
  styleUrls: ['./complaint.scss']
})
export class ComplaintComponent implements OnInit {
  form!: FormGroup;
  complaints: any[] = []; // example array to display complaints
  isLoading = false;
  successMessage = '';
  errorMessage = '';

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {}

  getStatusBadgeClass(status: string): string {
    switch (status?.toLowerCase()) {
      case 'resolved':
        return 'badge bg-success';
      case 'in progress':
        return 'badge bg-warning text-dark';
      case 'pending':
        return 'badge bg-secondary';
      default:
        return 'badge bg-light text-dark';
    }
  }

  get resolvedComplaintsCount(): number {
    return this.complaints?.filter(c => c.status === 'Resolved').length || 0;
  }

  ngOnInit(): void {
    console.log('📋 ComplaintComponent: Initializing...');

    // Check if user is logged in
    const isLoggedIn = this.authService.isLoggedIn();
    console.log('🔐 ComplaintComponent: User logged in:', isLoggedIn);
    console.log('👤 ComplaintComponent: Current user:', this.authService.currentUser);

    // Check localStorage directly
    const userData = localStorage.getItem('student_portal_user');
    const loginTime = localStorage.getItem('student_portal_login_time');
    console.log('💾 ComplaintComponent: localStorage user data:', userData);
    console.log('⏰ ComplaintComponent: localStorage login time:', loginTime);

    if (!isLoggedIn) {
      console.log('❌ ComplaintComponent: User not logged in, redirecting to login');
      this.errorMessage = 'Please login to access the complaint system.';
      this.router.navigate(['/login']);
      return;
    }

    console.log('✅ ComplaintComponent: User authenticated, proceeding with initialization');

    this.form = this.fb.group({
      title: ['', Validators.required],
      category: ['', Validators.required],
      description: ['', [Validators.required, Validators.minLength(10)]]
    });

    console.log('📝 ComplaintComponent: Form initialized');

    // Load user's complaints from database
    console.log('📡 ComplaintComponent: Loading user complaints...');
    this.authService.getUserComplaints().subscribe({
      next: (response: any) => {
        console.log('📥 ComplaintComponent: Complaints response:', response);
        if (response.success) {
          this.complaints = response.complaints || [];
          console.log('✅ ComplaintComponent: Loaded', this.complaints.length, 'complaints');
        } else {
          console.error('❌ ComplaintComponent: Failed to load complaints:', response.message);
          this.complaints = [];
        }
      },
      error: (error) => {
        console.error('❌ ComplaintComponent: Error loading complaints:', error);
        this.complaints = [];
      }
    });

    console.log('🎉 ComplaintComponent: Initialization complete');
  }

  submit(): void {
    // Double-check authentication before submission
    if (!this.authService.isLoggedIn()) {
      this.errorMessage = 'Your session has expired. Please login again.';
      this.router.navigate(['/login']);
      return;
    }

    if (this.form.valid) {
      this.isLoading = true;
      this.errorMessage = '';
      this.successMessage = '';

      // Create complaint object
      const complaint = {
        ...this.form.value,
        id: Date.now(),
        status: 'Pending',
        created_at: new Date().toISOString()
      };

      // Save complaint using AuthService
      this.authService.saveUserComplaint(complaint).subscribe({
        next: (response: any) => {
          this.isLoading = false;
          if (response.success) {
            this.successMessage = 'Complaint submitted successfully! We will review it shortly.';
            this.complaints.unshift({
              ...complaint,
              id: response.complaint?.id || complaint.id
            }); // Add to top of list
            this.form.reset();
          } else {
            this.errorMessage = response.message || 'Failed to submit complaint.';
          }
        },
        error: (error) => {
          this.isLoading = false;
          this.errorMessage = 'Failed to submit complaint. Please try again.';
          console.error('Complaint submission error:', error);
        }
      });
    } else {
      this.errorMessage = 'Please fill all required fields correctly.';
      // Mark all fields as touched to show validation errors
      Object.keys(this.form.controls).forEach(key => {
        this.form.get(key)?.markAsTouched();
      });
    }
  }

  resetForm(): void {
    // Reset the form to its initial state
    this.form.reset();

    // Clear all validation states
    Object.keys(this.form.controls).forEach(key => {
      const control = this.form.get(key);
      control?.setErrors(null);
      control?.markAsUntouched();
      control?.markAsPristine();
    });

    // Clear any success/error messages
    this.successMessage = '';
    this.errorMessage = '';

    // Reset loading state
    this.isLoading = false;

    console.log('Form has been reset successfully');
  }
}
