import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ApiService {
  private base = environment.apiUrl;

  constructor(private http: HttpClient) {}

  register(payload: any): Observable<any> {
    // Real API call to PHP backend
    return this.http.post(`${this.base}/register.php`, payload);
  }

  login(payload: any): Observable<any> {
    // Real API call to PHP backend
    return this.http.post(`${this.base}/login.php`, payload);
  }

  submitComplaint(payload: any): Observable<any> {
    // Real API call to PHP backend
    return this.http.post(`${this.base}/submit_complaint.php`, payload);
  }

  getComplaints(studentId?: number): Observable<any> {
    // Real API call to PHP backend
    const url = studentId ? `${this.base}/get_complaints.php?student_id=${studentId}` : `${this.base}/get_complaints.php`;
    return this.http.get(url);
  }
}
