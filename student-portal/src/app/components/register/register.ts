import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterModule],  // ✅ add RouterModule for routerLink
  templateUrl: './register.html',
  styleUrls: ['./register.scss']
})
export class RegisterComponent implements OnInit {
  form!: FormGroup;
  isLoading = false;
  successMessage = '';
  errorMessage = '';

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.form = this.fb.group({
      name: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      roll_no: ['', Validators.required],
      department: ['', Validators.required],
      phone: ['', Validators.required],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  onSubmit(): void {
    if (this.form.valid) {
      this.isLoading = true;
      this.errorMessage = '';
      this.successMessage = '';

      this.authService.register(this.form.value).subscribe({
        next: (response: any) => {
          this.isLoading = false;
          if (response.success) {
            this.successMessage = 'Registration successful! Redirecting to login...';
            setTimeout(() => {
              this.router.navigate(['/login']);
            }, 2000);
          } else {
            // Handle validation errors from backend
            if (response.errors && Array.isArray(response.errors)) {
              this.errorMessage = 'Validation failed:\n' + response.errors.join('\n');
            } else {
              this.errorMessage = response.message || 'Registration failed. Please try again.';
            }
          }
        },
        error: (err: any) => {
          this.isLoading = false;
          console.error('Registration error:', err);

          // Handle HTTP errors and validation errors
          if (err.error) {
            if (err.error.errors && Array.isArray(err.error.errors)) {
              this.errorMessage = 'Validation failed:\n' + err.error.errors.join('\n');
            } else if (err.error.message) {
              this.errorMessage = err.error.message;
            } else {
              this.errorMessage = 'Registration failed. Please check your input and try again.';
            }
          } else {
            this.errorMessage = 'Network error. Please check your connection and try again.';
          }
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
}
