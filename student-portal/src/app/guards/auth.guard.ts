import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  canActivate(): boolean {
    console.log('🔐 AuthGuard: Checking authentication...');

    const isLoggedIn = this.authService.isLoggedIn();
    console.log('✅ AuthGuard: isLoggedIn() result:', isLoggedIn);

    if (isLoggedIn) {
      console.log('✅ AuthGuard: User is logged in, allowing access');
      return true;
    } else {
      console.log('❌ AuthGuard: User is not logged in, blocking access');

      // Clear any stale data
      localStorage.removeItem('student_portal_user');
      localStorage.removeItem('student_portal_login_time');
      console.log('🧹 AuthGuard: Cleared stale localStorage data');

      // Show alert message
      alert('Please login first to access the complaint system.');

      // Redirect to login page
      this.router.navigate(['/login']);
      return false;
    }
  }
}
