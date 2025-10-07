import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { AuthService, User } from '../../services/auth.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterModule],  // ✅ CommonModule needed for *ngFor and *ngIf
  templateUrl: './home.html',
  styleUrls: ['./home.scss']
})
export class HomeComponent implements OnInit, OnDestroy {
  currentUser: User | null = null;
  showWelcomeMessage = true;
  private userSubscription: Subscription = new Subscription();

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    // Subscribe to current user changes
    this.userSubscription = this.authService.currentUser$.subscribe(user => {
      this.currentUser = user;
      // Show welcome message when user logs in
      if (user) {
        this.showWelcomeMessage = true;
      }
    });
  }

  ngOnDestroy(): void {
    // Unsubscribe to prevent memory leaks
    if (this.userSubscription) {
      this.userSubscription.unsubscribe();
    }
  }

  logout(): void {
    this.authService.logout();
    this.router.navigate(['/login']);
  }

  dismissWelcomeMessage(): void {
    this.showWelcomeMessage = false;
  }

  cards = [
  {
    title: 'Quick Registration',
    text: 'Create your student account with minimal steps and get access immediately.',
    link: '/register'
  },
  {
    title: 'Efficient Login',
    text: 'Securely log in and access all your student services in one place.',
    link: '/login'
  },
  {
    title: 'Submit Complaints',
    text: 'Report issues related to academics, hostel, or administration with ease.',
    link: '/complaint'
  },
  {
    title: 'Real-Time Updates',
    text: 'Receive notifications about complaint status changes and administrative announcements.'
  },
  {
    title: 'Profile Management',
    text: 'Update your personal information and track your academic records seamlessly.'
  },
  {
    title: 'Support & Guidance',
    text: 'Get instant help from the student support team whenever required.'
  }
];

}
