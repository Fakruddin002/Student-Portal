import { Component, OnInit, OnDestroy, HostListener } from '@angular/core';
import { RouterOutlet, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from './services/auth.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, RouterModule, CommonModule],
  templateUrl: './app.html',
  styleUrls: ['./app.scss']
})
export class AppComponent implements OnInit, OnDestroy {
  title = 'student-portal';
  currentYear: any;
  isLoggedIn = false;
  private authSubscription: Subscription | null = null;

  constructor(public authService: AuthService) {}

  ngOnInit() {
    this.currentYear = new Date().getFullYear();
    
    // Subscribe to auth state changes
    this.authSubscription = this.authService.currentUser$.subscribe(user => {
      this.isLoggedIn = !!user;
      console.log('Navigation auth state updated:', this.isLoggedIn);
    });
    
    // Initialize login state
    this.isLoggedIn = this.authService.isLoggedIn();
  }

  ngOnDestroy() {
    // Clean up subscriptions
    if (this.authSubscription) {
      this.authSubscription.unsubscribe();
    }
  }

  // Track user activity to reset session timeout
  @HostListener('document:click', ['$event'])
  @HostListener('document:keypress', ['$event'])
  @HostListener('document:mousemove', ['$event'])
  @HostListener('document:scroll', ['$event'])
  onUserActivity(event: Event) {
    // Reset session timeout on any user activity
    this.authService.resetSessionTimeout();
  }
  
  logout() {
    this.authService.logout();
  }
}
