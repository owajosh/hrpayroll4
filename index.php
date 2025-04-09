<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nextfleetdynamics - Bus Fleet Payroll Solutions</title>
  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.gstatic.com" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap"
    rel="stylesheet"
  />
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
  <style>
    /* Basic Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      color: #333;
      background-color: #f5f5f5;
      line-height: 1.6;
    }
    
    /* Navigation */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      background-color: #ffffff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 100;
    }
    
    .logo h2 {
      font-size: 1.5rem;
      font-weight: 600;
      color: #333;
    }
    
    .nav-links .btn {
      text-decoration: none;
      margin-left: 1rem;
      padding: 0.5rem 1rem;
      border: 2px solid #333;
      background: transparent;
      color: #333;
      transition: all 0.3s;
      border-radius: 4px;
      font-weight: 500;
    }
    
    .nav-links .btn:hover {
      background: #333;
      color: #fff;
      transform: translateY(-2px);
    }
    
    /* Hero Section with Same Background */
    .hero-section {
      position: relative;
      width: 100%;
      height: 100vh;
      background: url('css/bus-background.jpg') no-repeat center center/cover;
      background-attachment: fixed;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 60px;
    }
    
    /* Overlay */
    .hero-section .overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1;
    }
    
    .hero-content {
      position: relative;
      z-index: 2;
      text-align: center;
      color: #fff;
      max-width: 900px;
      padding: 0 2rem;
    }
    
    .hero-content h1 {
      font-size: 3.5rem;
      margin-bottom: 1.5rem;
      font-weight: 700;
      letter-spacing: 1px;
      text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .hero-content p {
      font-size: 1.3rem;
      margin-bottom: 2.5rem;
      font-weight: 300;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .cta-buttons {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 2rem;
    }
    
    .cta-btn {
      padding: 0.9rem 2rem;
      font-size: 1.1rem;
      font-weight: 500;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
    }
    
    .primary-btn {
      background: #0f4c81;
      color: white;
      box-shadow: 0 4px 6px rgba(15, 76, 129, 0.3);
    }
    
    .primary-btn:hover {
      background: #0c3c64;
      transform: translateY(-3px);
      box-shadow: 0 7px 14px rgba(15, 76, 129, 0.4);
    }
    
    .secondary-btn {
      background: transparent;
      color: white;
      border: 2px solid white;
    }
    
    .secondary-btn:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateY(-3px);
    }
    
    /* Features Section */
    .features-section {
      padding: 5rem 2rem;
      background: white;
    }
    
    .section-title {
      text-align: center;
      margin-bottom: 3rem;
    }
    
    .section-title h2 {
      font-size: 2.5rem;
      color: #333;
      margin-bottom: 1rem;
      font-weight: 600;
    }
    
    .section-title p {
      font-size: 1.1rem;
      color: #666;
      max-width: 700px;
      margin: 0 auto;
    }
    
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .feature-card {
      background: #f9f9f9;
      border-radius: 10px;
      padding: 2rem;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .feature-icon {
      font-size: 2.5rem;
      color: #0f4c81;
      margin-bottom: 1.5rem;
    }
    
    .feature-card h3 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
      color: #333;
    }
    
    .feature-card p {
      color: #666;
      font-size: 1rem;
    }
    
    /* Benefits Section */
    .benefits-section {
      padding: 5rem 2rem;
      background: #f0f5fb;
    }
    
    .benefits-container {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .benefit-column {
      flex: 1;
      min-width: 300px;
    }
    
    .benefit-item {
      display: flex;
      align-items: flex-start;
      margin-bottom: 2rem;
    }
    
    .benefit-icon {
      color: #0f4c81;
      font-size: 1.5rem;
      margin-right: 1rem;
      background: rgba(15, 76, 129, 0.1);
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      flex-shrink: 0;
    }
    
    .benefit-content h3 {
      margin-bottom: 0.5rem;
      font-size: 1.3rem;
    }
    
    /* How It Works Section */
    .how-it-works {
      padding: 5rem 2rem;
      background: white;
    }
    
    .steps-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 3rem;
      max-width: 1200px;
      margin: 0 auto;
      position: relative;
    }
    
    .step {
      flex: 1;
      min-width: 250px;
      text-align: center;
      position: relative;
      z-index: 1;
    }
    
    .step-number {
      width: 60px;
      height: 60px;
      background: #0f4c81;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: 600;
      margin: 0 auto 1.5rem;
    }
    
    .step h3 {
      margin-bottom: 1rem;
      font-size: 1.3rem;
    }
    
    /* Call to Action */
    .cta-section {
      padding: 5rem 2rem;
      background: linear-gradient(135deg, #0f4c81, #072b49);
      color: white;
      text-align: center;
    }
    
    .cta-content {
      max-width: 800px;
      margin: 0 auto;
    }
    
    .cta-content h2 {
      font-size: 2.5rem;
      margin-bottom: 1.5rem;
    }
    
    .cta-content p {
      font-size: 1.1rem;
      margin-bottom: 2rem;
      opacity: 0.9;
    }
    
    /* Footer */
    .footer {
      padding: 3rem 2rem;
      background-color: #1a1a1a;
      color: #f5f5f5;
    }
    
    .footer-content {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      max-width: 1200px;
      margin: 0 auto;
      gap: 2rem;
    }
    
    .footer-col {
      flex: 1;
      min-width: 200px;
    }
    
    .footer-col h3 {
      margin-bottom: 1.5rem;
      font-size: 1.3rem;
      position: relative;
    }
    
    .footer-col h3::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -0.5rem;
      width: 50px;
      height: 2px;
      background: #0f4c81;
    }
    
    .footer-col ul {
      list-style: none;
    }
    
    .footer-col ul li {
      margin-bottom: 0.75rem;
    }
    
    .footer-col ul li a {
      color: #ccc;
      text-decoration: none;
      transition: color 0.3s;
    }
    
    .footer-col ul li a:hover {
      color: #0f4c81;
    }
    
    .footer-bottom {
      text-align: center;
      padding-top: 2rem;
      margin-top: 2rem;
      border-top: 1px solid #333;
      color: #999;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .navbar {
        padding: 1rem;
      }
      
      .hero-content h1 {
        font-size: 2.5rem;
      }
      
      .cta-buttons {
        flex-direction: column;
        align-items: center;
      }
      
      .cta-btn {
        width: 100%;
        max-width: 300px;
      }
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar">
    <div class="logo">
      <h2>Nextfleetdynamics</h2>
    </div>
    <div class="nav-links">
      <a href="login.php" class="btn">Admin Login</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <header class="hero-section">
    <div class="overlay"></div>
    <div class="hero-content">
      <h1>Bus Fleet Payroll Solutions</h1>
      <p>Specialized payroll management for bus companies handling complex schedules, routes, and driver compensation packages.</p>
      <div class="cta-buttons">
        <a href="#features" class="cta-btn primary-btn">Explore System</a>
      </div>
    </div>
  </header>

  <!-- Features Section -->
  <section id="features" class="features-section">
    <div class="section-title">
      <h2>Payroll Features for Transportation Companies</h2>
      <p>Our platform is specifically designed for bus and transportation fleet management.</p>
    </div>
    
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-route"></i>
        </div>
        <h3>Route-Based Pay Calculation</h3>
        <p>Automatically calculate driver compensation based on routes, mileage, and service hours.</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-clock"></i>
        </div>
        <h3>Shift & Overtime Management</h3>
        <p>Track irregular hours, split shifts, and overnight routes with our transportation-specific timekeeping.</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-users"></i>
        </div>
        <h3>Driver Scheduling Integration</h3>
        <p>Seamlessly connect driver scheduling with payroll to eliminate manual data entry and errors.</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <h3>Transportation Compliance</h3>
        <p>Stay compliant with industry-specific regulations and tax requirements for transportation workers.</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-mobile-alt"></i>
        </div>
        <h3>Driver Mobile Access</h3>
        <p>Drivers can view pay stubs, schedules, and tax documents through our secure mobile portal.</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-chart-line"></i>
        </div>
        <h3>Fleet Analytics</h3>
        <p>Analyze labor costs by route, driver, and vehicle to optimize your transportation operations.</p>
      </div>
    </div>
  </section>

  <!-- Benefits Section -->
  <section class="benefits-section">
    <div class="section-title">
      <h2>Benefits for Bus Companies</h2>
      <p>Why transportation companies choose our specialized payroll solution</p>
    </div>
    
    <div class="benefits-container">
      <div class="benefit-column">
        <div class="benefit-item">
          <div class="benefit-icon">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="benefit-content">
            <h3>Reduce Payroll Processing Time by 85%</h3>
            <p>Automate complex calculations for routes, on-call time, and split shifts that would normally take days to process manually.</p>
          </div>
        </div>
        
        <div class="benefit-item">
          <div class="benefit-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="benefit-content">
            <h3>Eliminate Calculation Errors</h3>
            <p>Our system accurately handles complex transportation pay rules including differential pay for special routes and overtime calculations.</p>
          </div>
        </div>
        
        <div class="benefit-item">
          <div class="benefit-icon">
            <i class="fas fa-calendar-alt"></i>
          </div>
          <div class="benefit-content">
            <h3>Streamline Schedule Management</h3>
            <p>Connect scheduling and payroll systems to automatically update pay when routes or shifts change.</p>
          </div>
        </div>
      </div>
      
      <div class="benefit-column">
        <div class="benefit-item">
          <div class="benefit-icon">
            <i class="fas fa-clipboard-check"></i>
          </div>
          <div class="benefit-content">
            <h3>Transportation Compliance</h3>
            <p>Stay compliant with transportation-specific labor laws, DOT regulations, and tax requirements.</p>
          </div>
        </div>
        
        <div class="benefit-item">
          <div class="benefit-icon">
            <i class="fas fa-headset"></i>
          </div>
          <div class="benefit-content">
            <h3>Industry Expert Support</h3>
            <p>Get help from payroll specialists who understand the unique needs of bus and transportation companies.</p>
          </div>
        </div>
        
        <div class="benefit-item">
          <div class="benefit-icon">
            <i class="fas fa-tachometer-alt"></i>
          </div>
          <div class="benefit-content">
            <h3>Improve Driver Satisfaction</h3>
            <p>Accurate, on-time payments and transparent payroll information lead to higher driver retention and satisfaction.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section class="how-it-works">
    <div class="section-title">
      <h2>How Our System Works</h2>
      <p>Implementing our transportation payroll solution is straightforward and efficient.</p>
    </div>
    
    <div class="steps-container">
      <div class="step">
        <div class="step-number">1</div>
        <h3>Fleet Configuration</h3>
        <p>We'll set up your routes, driver categories, and pay rules specific to your bus company.</p>
      </div>
      
      <div class="step">
        <div class="step-number">2</div>
        <h3>Driver Onboarding</h3>
        <p>Import driver information and set up their unique pay structures and benefits.</p>
      </div>
      
      <div class="step">
        <div class="step-number">3</div>
        <h3>Route & Schedule Integration</h3>
        <p>Connect your existing scheduling systems with our payroll platform.</p>
      </div>
      
      <div class="step">
        <div class="step-number">4</div>
        <h3>Run & Distribute</h3>
        <p>Process payroll with a click and automatically distribute pay stubs to your drivers.</p>
      </div>
    </div>
  </section>

  <!-- Call to Action Section -->
  <section class="cta-section">
    <div class="cta-content">
      <h2>Ready to Streamline Your Bus Company's Payroll?</h2>
      <p>Join other transportation companies that have simplified their payroll process with our specialized solution. Get started today with a personalized demo.</p>
      <a href="contact.php" class="cta-btn primary-btn" style="background: white; color: #0f4c81;">Schedule Demo</a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-col">
        <h3>Company</h3>
        <ul>
          <li><a href="about.php">About Us</a></li>
          <li><a href="team.php">Our Team</a></li>
          <li><a href="careers.php">Careers</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>
      
      <div class="footer-col">
        <h3>Solutions</h3>
        <ul>
          <li><a href="driver-payroll.php">Driver Payroll</a></li>
          <li><a href="route-tracking.php">Route Tracking</a></li>
          <li><a href="fleet-management.php">Fleet Management</a></li>
          <li><a href="compliance.php">Transportation Compliance</a></li>
        </ul>
      </div>
      
      <div class="footer-col">
        <h3>Resources</h3>
        <ul>
          <li><a href="blog.php">Blog</a></li>
          <li><a href="guides.php">Transportation Guides</a></li>
          <li><a href="webinars.php">Webinars</a></li>
          <li><a href="faq.php">FAQ</a></li>
        </ul>
      </div>
      
      <div class="footer-col">
        <h3>Legal</h3>
        <ul>
          <li><a href="privacy.php">Privacy Policy</a></li>
          <li><a href="terms.php">Terms of Service</a></li>
          <li><a href="dot-compliance.php">DOT Compliance</a></li>
          <li><a href="security.php">Data Security</a></li>
        </ul>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>© 2025 Nextfleetdynamics - Bus Fleet Payroll Solutions. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>