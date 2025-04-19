# ExamFlow

<p align="center">
  <img src="img/logo.png" alt="ExamFlow Logo" width="180">
</p>

<p align="center">
  <b>Advanced Online Assessment Platform with Blockchain-Verified Credentials</b>
</p>

<p align="center">
  <a href="#overview">Overview</a> •
  <a href="#key-features">Key Features</a> •
  <a href="#anti-cheat-system">Anti-Cheat System</a> •
  <a href="#blockchain-integration">Blockchain Integration</a> •
  <a href="#email-notifications">Email Notifications</a> •
  <a href="#architecture">Architecture</a> •
  <a href="#installation">Installation</a> •
  <a href="#usage">Usage</a> •
  <a href="#security">Security</a> •
  <a href="#development">Development</a>
</p>

## Overview

ExamFlow is an enterprise-grade online assessment platform designed to provide secure, efficient, and transparent examination experiences for educational institutions. It addresses critical challenges in remote assessment through an innovative dual-panel system with blockchain verification and advanced anti-cheating measures.

### Problem Statement

Educational institutions conducting remote assessments face significant challenges:

- Time-intensive manual exam creation and assessment
- Resource waste with traditional paper-based methods
- Difficulty maintaining academic integrity in remote settings
- Delays in grading and result distribution

### Solution

ExamFlow delivers a comprehensive platform that streamlines the entire examination process while ensuring academic integrity through cutting-edge technologies.

## Key Features

### Administration Panel

- **Dashboard Analytics**: Real-time examination statistics and performance metrics
- **Exam Management**: Intuitive creation and configuration interface
- **Question Bank**: Secure repository with categorization and difficulty settings
- **Automated Assessment**: Instant grading with customizable scoring parameters
- **Performance Analysis**: Detailed insights into student performance patterns
- **Communication System**: Centralized announcement distribution
- **Integrity Monitoring**: Comprehensive violation tracking and reporting

### Student Experience

- **Streamlined Interface**: Distraction-free environment with intuitive navigation
- **Time Management**: Synchronized countdown timer with auto-submission
- **Progress Tracking**: Question-by-question navigation with completion indicators
- **Result Access**: Immediate post-submission scoring and performance analysis
- **Integrity Transparency**: Real-time integrity score visibility during examinations
- **Credential Generation**: On-demand blockchain certificate creation

### Mock Examination System

- **Practice Environment**: Auto-generated mock exams based on real assessments
- **Adaptive Practice**: Multiple mock tests per exam with varied questions
- **Self-Assessment**: Students can evaluate their readiness before actual exams
- **Performance Analytics**: Track progress and improvement across practice attempts
- **Realistic Experience**: Mock exams mirror the actual exam environment and format
- **Instant Feedback**: Immediate scoring and answer review after completion

## Anti-Cheat System

ExamFlow implements a sophisticated multi-layered anti-cheat system to ensure examination integrity.

### Detection Mechanisms

- **Tab Switching Detection**: Leverages the Visibility API to identify context switching
- **Window Focus Monitoring**: Tracks when the examination window loses focus
- **Combined Behavior Analysis**: Identifies suspicious patterns through temporal correlation
- **Full-Screen Enforcement**: Requires and monitors examination in full-screen mode

### Integrity Scoring

- **100-Point Scale**: Students begin with a perfect score that reflects examination integrity
- **Progressive Penalties**: Escalating deductions based on violation frequency and severity
- **Real-Time Transparency**: Students can view their current integrity score during the exam
- **Automatic Intervention**: Examination auto-submission if integrity falls below critical thresholds

### Classification System

Integrity scores translate to the following categories:

| Category           | Score Range | Interpretation                                   |
| ------------------ | ----------- | ------------------------------------------------ |
| Good               | 75-100      | Normal examination behavior                      |
| At-Risk            | 50-74       | Potentially suspicious patterns requiring review |
| Cheating Suspicion | 0-49        | Significant evidence of academic dishonesty      |

### Violation Penalties

#### Tab Switching

| Occurrences | Penalty    | Cumulative Impact |
| ----------- | ---------- | ----------------- |
| First       | -3 points  | -3 points         |
| Second      | -5 points  | -8 points         |
| Third       | -8 points  | -16 points        |
| Fourth+     | -15 points | -31+ points       |

#### Window Focus Loss

| Occurrences | Penalty   | Cumulative Impact |
| ----------- | --------- | ----------------- |
| First       | -2 points | -2 points         |
| Second      | -4 points | -6 points         |
| Third       | -6 points | -12 points        |
| Fourth+     | -8 points | -20+ points       |

#### Combined Violations

| Occurrences | Penalty    | Cumulative Impact |
| ----------- | ---------- | ----------------- |
| First       | -10 points | -10 points        |
| Second      | -15 points | -25 points        |
| Third+      | -20 points | -45+ points       |

## Blockchain Integration

ExamFlow utilizes blockchain technology to provide tamper-proof digital credentials.

### Certificate System

- **NFT Certification**: Each credential is minted as a unique non-fungible token
- **Ethereum Integration**: Utilizes Sepolia testnet for cost-effective verification
- **IPFS Storage**: Decentralized storage of certificate images and metadata
- **Integrity Verification**: Examination scores and integrity metrics permanently recorded
- **Marketplace Compatibility**: Certificates viewable on standard NFT platforms

### Technical Implementation

- **Smart Contract**: ERC-721 compliant for maximum compatibility
- **Metadata Standard**: Follows OpenSea metadata specifications
- **Storage Provider**: Pinata IPFS pinning service
- **Client Integration**: ethers.js for blockchain interaction

## Email Notifications

ExamFlow integrates an automated email notification system that sends certificates to students upon successful exam completion.

### Certificate Email System

- **Automated Delivery**: NFT certificates sent directly to students' email addresses
- **Attachment Format**: High-quality PNG certificates with blockchain verification details
- **Blockchain Links**: Direct links to OpenSea and Etherscan for certificate verification
- **Customized Content**: Personalized email messages with student and exam information
- **Secure Delivery**: Industry-standard SMTP with proper authentication

### Technical Implementation

- **Email Service**: Uses PHPMailer library with SMTP authentication
- **Template System**: Responsive HTML email templates with certificate details
- **Error Handling**: Comprehensive logging for troubleshooting delivery issues
- **Configuration**: Centralized email settings for easy maintenance

## Architecture

```
┌───────────────────┐      ┌────────────────┐      ┌───────────────────┐
│                   │      │                │      │                   │
│  ADMINISTRATION   │◄────►│    DATABASE    │◄────►│      STUDENT      │
│      PANEL        │      │                │      │      PORTAL       │
│                   │      └────────────────┘      │                   │
└───────────────────┘              │              └───────────────────┘
                                   │                        │
                                   ▼                        ▼
                          ┌────────────────┐      ┌───────────────────┐
                          │     ANTI-      │      │    BLOCKCHAIN     │
                          │     CHEAT      │      │    INTEGRATION    │
                          │     SYSTEM     │      │                   │
                          └────────────────┘      └───────────────────┘
                                  │                        │
                                  │                ┌────────┴───────┐
                                  │                ▼                ▼
                                  │         ┌─────────────┐  ┌─────────────┐
                                  │         │    IPFS     │  │  ETHEREUM   │
                                  │         │   STORAGE   │  │   NETWORK   │
                                  │         └─────────────┘  └─────────────┘
                                  ▼
                          ┌────────────────┐
                          │     MOCK       │
                          │     EXAM       │
                          │    SYSTEM      │
                          └────────────────┘
```

## Installation

### Prerequisites

- Web server with PHP 7.4+
- MySQL 5.7+ database
- Composer for PHP dependencies
- Internet connection for blockchain features

### Setup Process

1. **Clone the repository**

   ```bash
   git clone https://github.com/your-organization/examflow.git
   cd examflow
   ```

2. **Configure database**

   ```bash
   # Create database
   mysql -u root -p -e "CREATE DATABASE db_eval CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

   # Import schema
   mysql -u root -p db_eval < database/db_eval.sql
   ```

3. **Set up mock exam tables**

   ```bash
   # Run the installation script for mock exam tables
   php install_mock_tables.php
   ```

4. **Configure environment**

   ```bash
   # Copy example configuration
   cp config.example.php config.php

   # Edit configuration with your database details
   nano config.php
   ```

5. **Configure email notifications**

   ```bash
   # Copy example email configuration
   cp utils/email_config.example.php utils/email_config.php

   # Edit with your email credentials
   nano utils/email_config.php
   ```

   For Gmail accounts:
   - Enable 2-Factor Authentication on your Google account
   - Create an App Password at https://myaccount.google.com/apppasswords
   - Use the App Password (not your regular Gmail password) in the config file

6. **Set up blockchain features** (optional)

   ```bash
   # Create environment file
   cp students/.env.example students/.env

   # Add your Pinata API keys and Ethereum credentials
   nano students/.env
   ```

7. **Deploy to web server**
   ```bash
   # Ensure correct permissions
   chmod -R 755 .
   chmod -R 777 uploads/
   ```

## Usage

### For Instructors

1. Access the administration panel at `/teachers/login.php`
2. Create new examinations with customizable parameters
3. Manage question banks and student registrations
4. Generate practice mock exams for students from existing assessments
5. Monitor real-time examination progress
6. Review integrity reports and performance analytics

### For Students

1. Log in to the student portal at `/login_student.php`
2. View available examinations and practice mock exams on the dashboard
3. Take mock tests to prepare for upcoming assessments
4. Enter examination environment when ready
5. Navigate through questions and submit answers
6. Review results and generate blockchain certificates

## Security

ExamFlow incorporates comprehensive security measures:

- **Authentication**: Secure session management with timeout protection
- **Data Protection**: Input validation against injection attacks
- **Examination Integrity**: Multi-layered anti-cheat system
- **Credential Security**: Tamper-proof blockchain verification
- **Environmental Security**: Full-screen enforcement and monitoring

## Development

### Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **UI Components**: Boxicons
- **Blockchain**: Ethereum (Sepolia) with ethers.js
- **Storage**: IPFS via Pinata API
- **Email**: PHPMailer with SMTP authentication

### Key Components

- **Examination Engine**: `students/examportal.php`
- **Anti-Cheat System**: `students/log_violation.php`
- **Certificate Generation**: `students/generate_certificate.php`
- **Blockchain Integration**: `students/mint_nft.php`
- **Email Notification**: `utils/mailer.php`
- **Violation Reporting**: `teachers/view_violations.php`

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Developed by Team Cerebro
- Special thanks to all contributors and testers

---

<p align="center">Made with precision and care by Team Cerebro</p>
