<body>
    <!-- Control Panel -->
    <div class="control-panel">
        <h1>📋 Student Health Report Generator</h1>
        <p>Generate comprehensive health reports and download as PDF using your browser's print function.</p>
        <div class="button-container">
            <button class="btn btn-primary" onclick="window.print()">
                🖨️ Print / Save as PDF
            </button>
            <button class="btn btn-secondary" onclick="updateDate()">
                📅 Update Date
            </button>
            <button class="btn btn-secondary" onclick="fillSampleData()">
                📝 Load Sample Data
            </button>
        </div>
    </div>

    <!-- Report Content -->
    <div class="report-container">
        <!-- Report Header -->
        <div class="report-header">
            <div class="report-logo">⚕️</div>
            <h1 class="report-title">STUDENT HEALTH REPORT</h1>
            <p class="report-subtitle">Comprehensive Medical Records & Health Assessment</p>
            <div class="report-info">
                <div>Report ID: <span>SHR-2026-001234</span></div>
                <div>Date: <span id="reportDate">March 19, 2026</span></div>
                <div>Academic Year: <span>2025-2026</span></div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="section">
            <div class="section-header">
                <span class="section-icon">👤</span>
                <span>Student Information</span>
            </div>
            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Student ID</div>
                    <div class="info-value">STU-2026-4789</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">Sarah Elizabeth Johnson</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Date of Birth</div>
                    <div class="info-value">April 15, 2010</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Age</div>
                    <div class="info-value">15 years old</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Grade Level</div>
                    <div class="info-value">10th Grade</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Blood Group</div>
                    <div class="info-value">O+ Positive</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Parent/Guardian</div>
                    <div class="info-value">Michael Johnson</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Emergency Contact</div>
                    <div class="info-value">+1 (555) 123-4567</div>
                </div>
            </div>
        </div>

        <!-- Health Summary -->
        <div class="section">
            <div class="section-header">
                <span class="section-icon">📊</span>
                <span>Health Summary Overview</span>
            </div>
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-label">Total Visits</div>
                    <div class="summary-value">12</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Vaccination Rate</div>
                    <div class="summary-value">100%</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Active Issues</div>
                    <div class="summary-value">1</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Last Check-up</div>
                    <div class="summary-value" style="font-size: 20px;">15 Days</div>
                </div>
            </div>
        </div>

        <!-- Physical Examination -->
        <div class="section">
            <div class="section-header">
                <span class="section-icon">🏥</span>
                <span>Physical Examination Results</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Measured Value</th>
                        <th>Normal Range</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Height</strong></td>
                        <td>162 cm (5'4")</td>
                        <td>155-170 cm</td>
                        <td><span class="badge badge-normal">Normal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Weight</strong></td>
                        <td>52 kg (114 lbs)</td>
                        <td>45-60 kg</td>
                        <td><span class="badge badge-normal">Normal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Body Mass Index (BMI)</strong></td>
                        <td>19.8</td>
                        <td>18.5-24.9</td>
                        <td><span class="badge badge-normal">Normal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Blood Pressure</strong></td>
                        <td>118/76 mmHg</td>
                        <td>90-120/60-80 mmHg</td>
                        <td><span class="badge badge-normal">Normal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Heart Rate</strong></td>
                        <td>72 bpm</td>
                        <td>60-100 bpm</td>
                        <td><span class="badge badge-normal">Normal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Respiratory Rate</strong></td>
                        <td>16 breaths/min</td>
                        <td>12-20 breaths/min</td>
                        <td><span class="badge badge-normal">Normal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Body Temperature</strong></td>
                        <td>98.4°F (36.9°C)</td>
                        <td>97.8-99.1°F</td>
                        <td><span class="badge badge-normal">Normal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Vision - Right Eye</strong></td>
                        <td>20/20</td>
                        <td>20/20</td>
                        <td><span class="badge badge-normal">Normal</span></td>
                    </tr>
                    <tr>
                        <td><strong>Vision - Left Eye</strong></td>
                        <td>20/20</td>
                        <td>20/20</td>
                        <td><span class="badge badge-normal">Normal</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Vaccination Records -->
        <div class="section">
            <div class="section-header">
                <span class="section-icon">💉</span>
                <span>Vaccination Records</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Vaccine Name</th>
                        <th>Date Administered</th>
                        <th>Next Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Tetanus Toxoid (TT)</strong></td>
                        <td>January 15, 2025</td>
                        <td>January 15, 2035</td>
                        <td><span class="badge badge-completed">Completed</span></td>
                    </tr>
                    <tr>
                        <td><strong>Hepatitis B (HepB)</strong></td>
                        <td>March 10, 2024</td>
                        <td>Lifetime Protection</td>
                        <td><span class="badge badge-completed">Completed</span></td>
                    </tr>
                    <tr>
                        <td><strong>MMR (Measles, Mumps, Rubella)</strong></td>
                        <td>September 5, 2023</td>
                        <td>Lifetime Protection</td>
                        <td><span class="badge badge-completed">Completed</span></td>
                    </tr>
                    <tr>
                        <td><strong>HPV Vaccine (Dose 1 of 2)</strong></td>
                        <td>November 20, 2025</td>
                        <td>May 20, 2026</td>
                        <td><span class="badge badge-pending">Dose 2 Pending</span></td>
                    </tr>
                    <tr>
                        <td><strong>Influenza (Annual)</strong></td>
                        <td>October 1, 2025</td>
                        <td>October 1, 2026</td>
                        <td><span class="badge badge-completed">Up to Date</span></td>
                    </tr>
                    <tr>
                        <td><strong>COVID-19 Booster</strong></td>
                        <td>June 15, 2025</td>
                        <td>June 15, 2026</td>
                        <td><span class="badge badge-completed">Up to Date</span></td>
                    </tr>
                    <tr>
                        <td><strong>Meningococcal (MenACWY)</strong></td>
                        <td>August 22, 2024</td>
                        <td>August 22, 2029</td>
                        <td><span class="badge badge-completed">Completed</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Health Compliance Progress -->
        <div class="section">
            <div class="section-header">
                <span class="section-icon">📈</span>
                <span>Health Compliance Progress</span>
            </div>
            <div class="chart-container">
                <div class="chart-item">
                    <div class="chart-header">
                        <span class="chart-label">Vaccination Compliance</span>
                        <span class="chart-percentage">95%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 95%;"></div>
                    </div>
                </div>
                <div class="chart-item">
                    <div class="chart-header">
                        <span class="chart-label">Annual Physical Examination</span>
                        <span class="chart-percentage">100%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 100%;"></div>
                    </div>
                </div>
                <div class="chart-item">
                    <div class="chart-header">
                        <span class="chart-label">Dental Check-ups</span>
                        <span class="chart-percentage">100%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 100%;"></div>
                    </div>
                </div>
                <div class="chart-item">
                    <div class="chart-header">
                        <span class="chart-label">Vision Screening</span>
                        <span class="chart-percentage">100%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 100%;"></div>
                    </div>
                </div>
                <div class="chart-item">
                    <div class="chart-header">
                        <span class="chart-label">Mental Health Assessment</span>
                        <span class="chart-percentage">85%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 85%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical History Timeline -->
        <div class="section">
            <div class="section-header">
                <span class="section-icon">📅</span>
                <span>Recent Medical History Timeline</span>
            </div>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-date">📍 March 4, 2026</div>
                    <div class="timeline-content">
                        <div class="timeline-title">Annual Physical Examination</div>
                        <div class="timeline-description">
                            Comprehensive physical examination conducted by Dr. Emily Roberts. All vital signs within normal range. 
                            Height: 162cm, Weight: 52kg, BMI: 19.8. Student is in excellent health. Recommended maintaining 
                            active lifestyle and balanced nutrition with increased calcium and vitamin D intake for optimal bone health.
                        </div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">📍 February 12, 2026</div>
                    <div class="timeline-content">
                        <div class="timeline-title">Vision & Hearing Screening</div>
                        <div class="timeline-description">
                            Annual vision and hearing screening performed. Eye examination results: 20/20 vision for both eyes. 
                            No corrective lenses required. Hearing test passed at all frequencies. No concerns noted.
                        </div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">📍 January 28, 2026</div>
                    <div class="timeline-content">
                        <div class="timeline-title">Minor Sports Injury - Sprained Ankle</div>
                        <div class="timeline-description">
                            Student sustained mild sprain to left ankle during physical education class. RICE protocol applied 
                            immediately (Rest, Ice, Compression, Elevation). Parent notified. Student advised to rest for 3 days 
                            and avoid strenuous activities. Follow-up visit on February 4: Fully recovered, cleared for all activities.
                        </div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">📍 November 20, 2025</div>
                    <div class="timeline-content">
                        <div class="timeline-title">HPV Vaccination - First Dose</div>
                        <div class="timeline-description">
                            First dose of HPV vaccine administered as part of adolescent immunization program. No immediate adverse 
                            reactions observed during 15-minute post-vaccination monitoring period. Parent provided with information 
                            sheet. Second dose scheduled for May 20, 2026.
                        </div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">📍 October 1, 2025</div>
                    <div class="timeline-content">
                        <div class="timeline-title">Annual Influenza Vaccination</div>
                        <div class="timeline-description">
                            Seasonal influenza vaccine administered as part of school-wide immunization campaign. No complications 
                            reported. Student educated on importance of annual flu vaccination for community health.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Allergies & Medical Conditions -->
        <div class="section">
            <div class="section-header">
                <span class="section-icon">⚠️</span>
                <span>Known Allergies & Medical Conditions</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Condition/Allergen</th>
                        <th>Severity</th>
                        <th>Management Plan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Food Allergy</strong></td>
                        <td>Peanuts & Tree Nuts</td>
                        <td><span class="badge badge-overdue">Severe/Anaphylaxis</span></td>
                        <td>EpiPen prescribed and available in nurse's office. Strict avoidance of all peanut products. 
                            Emergency action plan on file.</td>
                    </tr>
                    <tr>
                        <td><strong>Environmental Allergy</strong></td>
                        <td>Seasonal Pollen (Spring)</td>
                        <td><span class="badge badge-pending">Moderate</span></td>
                        <td>Over-the-counter antihistamines as needed during allergy season. 
                            Monitor symptoms and report if severe.</td>
                    </tr>
                    <tr>
                        <td><strong>Respiratory Condition</strong></td>
                        <td>Exercise-Induced Asthma (Mild)</td>
                        <td><span class="badge badge-pending">Moderate</span></td>
                        <td>Albuterol inhaler prescribed. One spare inhaler kept in nurse's office. 
                            Pre-medication before intense physical activity as needed.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Health Recommendations -->
        <div class="section">
            <div class="section-header">
                <span class="section-icon">💡</span>
                <span>Medical Recommendations & Action Items</span>
            </div>
            <div class="recommendations-box">
                <ul>
                    <li><strong>Urgent:</strong> Complete HPV vaccine second dose by May 20, 2026 (scheduled appointment confirmed)</li>
                    <li>Continue daily physical activity - minimum 60 minutes of moderate to vigorous exercise recommended</li>
                    <li>Maintain balanced diet with emphasis on calcium-rich foods and vitamin D for adolescent bone development</li>
                    <li>Always carry EpiPen auto-injector due to severe peanut allergy - ensure unexpired device</li>
                    <li>Schedule next routine dental examination within 6 months (by September 2026)</li>
                    <li>Next annual physical examination due in March 2027</li>
                    <li>Continue monitoring asthma symptoms - keep rescue inhaler accessible during physical activities</li>
                    <li>Adequate sleep (8-10 hours nightly) and stress management practices recommended for overall wellness</li>
                    <li>Annual flu vaccination recommended each fall (next due October 2026)</li>
                    <li>Update emergency contact information if guardian phone number changes</li>
                </ul>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Dr. Emily Roberts, RN</div>
                <div class="signature-title">School Nurse | License #RN-87654</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Dr. James Anderson, MD</div>
                <div class="signature-title">School Medical Officer | License #MD-12345</div>
            </div>
        </div>

        <!-- Report Footer -->
        <div class="report-footer">
            <p class="footer-note">
                This report contains confidential medical information and is intended solely for authorized 
                school health personnel and the student's parent/guardian. Unauthorized disclosure is prohibited.
            </p>
            <div class="footer-contact">
                <strong>Student Health Management System</strong><br>
                Academic Year 2025-2026<br>
                Springfield High School Health Center<br>
                Email: health@springfield-hs.edu | Phone: +1 (555) 987-6543<br>
                Address: 123 Education Blvd, Springfield, State 12345
            </div>
        </div>
    </div>

    <script>
        // Update current date
        function updateDate() {
            const today = new Date();
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('reportDate').textContent = today.toLocaleDateString('en-US', options);
            alert('Date updated to current date!');
        }

        // Load sample data (you can customize this)
        function fillSampleData() {
            updateDate();
            alert('Sample data loaded successfully! Click "Print / Save as PDF" to download.');
        }

        // Auto-update date on page load
        window.onload = function() {
            updateDate();
        };
    </script>
</body>
</html>
