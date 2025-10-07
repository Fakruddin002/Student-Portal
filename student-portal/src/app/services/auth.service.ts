import { Injectable, Inject, PLATFORM_ID } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { BehaviorSubject, Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../environments/environment';
import { isPlatformBrowser } from '@angular/common';

export interface User {
  id: number;
  name: string;
  email: string;
  roll_no?: string;
  department?: string;
  phone?: string;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private readonly SESSION_TIMEOUT = 30 * 60 * 1000; // 30 minutes in milliseconds
  private readonly USER_KEY = 'student_portal_user';
  private readonly LOGIN_TIME_KEY = 'student_portal_login_time';
  private readonly baseUrl = environment.apiUrl;

  private currentUserSubject = new BehaviorSubject<User | null>(this.getUserFromStorage());
  public currentUser$ = this.currentUserSubject.asObservable();

  private sessionTimeoutId: any;

  constructor(
    private http: HttpClient,
    private router: Router,
    @Inject(PLATFORM_ID) private platformId: Object
  ) {
    console.log('🔧 AuthService: Constructor called at', new Date().toISOString());
    console.log('🌐 AuthService: Platform ID:', this.platformId);
    console.log('💾 AuthService: Is platform browser:', isPlatformBrowser(this.platformId));

    // Initialize with stored data
    const storedUser = this.getUserFromStorage();
    const storedLoginTime = this.getLoginTimeFromStorage();
    console.log('👤 AuthService: Stored user on init:', storedUser);
    console.log('⏰ AuthService: Stored login time on init:', storedLoginTime);
    console.log('🔄 AuthService: Using sessionStorage (cleared on browser close)');

    // Update the BehaviorSubject with stored data
    if (storedUser) {
      this.currentUserSubject.next(storedUser);
      console.log('✅ AuthService: BehaviorSubject updated with stored user');
    }

    this.initializeSessionTimeout();
    console.log('✅ AuthService: Initialization complete');
  }

  // Get current user
  get currentUser(): User | null {
    return this.currentUserSubject.value;
  }

  // Check if user is logged in
  isLoggedIn(): boolean {
    console.log('🔍 AuthService.isLoggedIn(): Checking authentication status');

    const user = this.getUserFromStorage();
    const loginTime = this.getLoginTimeFromStorage();

    console.log('👤 AuthService.isLoggedIn(): User from storage:', user);
    console.log('⏰ AuthService.isLoggedIn(): Login time from storage:', loginTime);

    if (!user || !loginTime) {
      console.log('❌ AuthService.isLoggedIn(): No user or login time found');
      return false;
    }

    // Check if session has expired
    const now = Date.now();
    const sessionAge = now - loginTime;
    console.log('⏳ AuthService.isLoggedIn(): Session age:', sessionAge, 'ms');
    console.log('⏰ AuthService.isLoggedIn(): Session timeout:', this.SESSION_TIMEOUT, 'ms');

    if (sessionAge > this.SESSION_TIMEOUT) {
      console.log('⏰ AuthService.isLoggedIn(): Session expired, logging out');
      this.logout();
      return false;
    }

    console.log('✅ AuthService.isLoggedIn(): User is logged in');
    return true;
  }

  // Login user
  login(userData: any): Observable<any> {
    return this.http.post(`${this.baseUrl}/login.php`, userData).pipe(
      map((response: any) => {
        if (response.success) {
          const user: User = response.user;

          // Store user data in sessionStorage (cleared when browser closes)
          if (isPlatformBrowser(this.platformId)) {
            sessionStorage.setItem(this.USER_KEY, JSON.stringify(user));
            sessionStorage.setItem(this.LOGIN_TIME_KEY, Date.now().toString());
          }

          // Update current user
          this.currentUserSubject.next(user);

          // Start session timeout
          this.startSessionTimeout();

          return response;
        } else {
          throw new Error(response.message || 'Login failed');
        }
      }),
      catchError(error => {
        console.error('Login error:', error);
        return throwError(() => error);
      })
    );
  }

  // Register user
  register(userData: any): Observable<any> {
    return this.http.post(`${this.baseUrl}/register.php`, userData).pipe(
      map((response: any) => {
        if (response.success) {
          return response;
        } else {
          throw new Error(response.message || 'Registration failed');
        }
      }),
      catchError(error => {
        console.error('Registration error:', error);
        return throwError(() => error);
      })
    );
  }

  // Logout user
  logout(): void {
    if (isPlatformBrowser(this.platformId)) {
      sessionStorage.removeItem(this.USER_KEY);
      sessionStorage.removeItem(this.LOGIN_TIME_KEY);
    }
    this.currentUserSubject.next(null);
    this.clearSessionTimeout();
    this.router.navigate(['/login']);
  }

  // Update user profile
  updateProfile(userData: Partial<User>): void {
    const currentUser = this.currentUser;
    if (currentUser && isPlatformBrowser(this.platformId)) {
      const updatedUser = { ...currentUser, ...userData };
      sessionStorage.setItem(this.USER_KEY, JSON.stringify(updatedUser));
      this.currentUserSubject.next(updatedUser);
    }
  }

  // Get stored complaints for user
  getUserComplaints(): Observable<any> {
    const user = this.currentUser;
    if (user) {
      return this.http.get(`${this.baseUrl}/get_complaints.php?student_id=${user.id}`);
    }
    return new Observable(observer => {
      observer.next([]);
      observer.complete();
    });
  }

  // Save complaint for user
  saveUserComplaint(complaint: any): Observable<any> {
    const user = this.currentUser;
    if (user) {
      const complaintData = {
        ...complaint,
        student_id: user.id
      };
      return this.http.post(`${this.baseUrl}/submit_complaint.php`, complaintData);
    }
    return new Observable(observer => {
      observer.error('User not logged in');
    });
  }

  // Private methods
  private getUserFromStorage(): User | null {
    console.log('💾 AuthService.getUserFromStorage(): Called');

    if (!isPlatformBrowser(this.platformId)) {
      console.log('🌐 AuthService.getUserFromStorage(): Not in browser environment');
      return null;
    }

    const userStr = sessionStorage.getItem(this.USER_KEY);
    console.log('📦 AuthService.getUserFromStorage(): Raw user string:', userStr);

    if (!userStr) {
      console.log('❌ AuthService.getUserFromStorage(): No user data in sessionStorage');
      return null;
    }

    try {
      const user = JSON.parse(userStr);
      console.log('✅ AuthService.getUserFromStorage(): Parsed user:', user);
      return user;
    } catch (error) {
      console.error('❌ AuthService.getUserFromStorage(): Error parsing user data:', error);
      return null;
    }
  }

  private getLoginTimeFromStorage(): number | null {
    console.log('⏰ AuthService.getLoginTimeFromStorage(): Called');

    if (!isPlatformBrowser(this.platformId)) {
      console.log('🌐 AuthService.getLoginTimeFromStorage(): Not in browser environment');
      return null;
    }

    const timeStr = sessionStorage.getItem(this.LOGIN_TIME_KEY);
    console.log('📦 AuthService.getLoginTimeFromStorage(): Raw time string:', timeStr);

    if (!timeStr) {
      console.log('❌ AuthService.getLoginTimeFromStorage(): No login time in sessionStorage');
      return null;
    }

    try {
      const time = parseInt(timeStr, 10);
      console.log('✅ AuthService.getLoginTimeFromStorage(): Parsed time:', time);
      return time;
    } catch (error) {
      console.error('❌ AuthService.getLoginTimeFromStorage(): Error parsing time:', error);
      return null;
    }
  }

  private storeUserData(user: User): void {
    // In a real app, this would store in a database
    // For demo purposes, we'll store in sessionStorage with a users key
    if (isPlatformBrowser(this.platformId)) {
      const usersKey = 'student_portal_users';
      const users = JSON.parse(sessionStorage.getItem(usersKey) || '[]');
      users.push(user);
      sessionStorage.setItem(usersKey, JSON.stringify(users));
    }
  }

  private initializeSessionTimeout(): void {
    if (this.isLoggedIn()) {
      this.startSessionTimeout();
    }
  }

  private startSessionTimeout(): void {
    this.clearSessionTimeout();
    this.sessionTimeoutId = setTimeout(() => {
      this.logout();
      alert('Your session has expired due to inactivity. Please login again.');
    }, this.SESSION_TIMEOUT);
  }

  private clearSessionTimeout(): void {
    if (this.sessionTimeoutId) {
      clearTimeout(this.sessionTimeoutId);
      this.sessionTimeoutId = null;
    }
  }

  // Reset session timeout on user activity
  resetSessionTimeout(): void {
    if (this.isLoggedIn()) {
      this.startSessionTimeout();
    }
  }

  // Force refresh current user from storage (useful for debugging)
  refreshCurrentUser(): void {
    console.log('🔄 AuthService: Force refreshing current user from storage');
    const storedUser = this.getUserFromStorage();
    if (storedUser) {
      this.currentUserSubject.next(storedUser);
      console.log('✅ AuthService: Current user refreshed:', storedUser);
    } else {
      this.currentUserSubject.next(null);
      console.log('❌ AuthService: No stored user found, set to null');
    }
  }
}
