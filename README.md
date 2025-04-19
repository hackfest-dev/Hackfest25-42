# ExamFlow

<p align="center">
  <img src="img/logo.png" alt="ExamFlow Logo" width="200">
</p>

<p align="center">
  <b>The Ultimate Assessment Platform with Blockchain-Verified Credentials & Advanced Proctoring</b>
</p>

<p align="center">
  <a href="#blockchain-verified-credentials">Blockchain Verification</a> •
  <a href="#integrity-scoring">Integrity Scoring</a> •
  <a href="#advanced-proctoring">Proctoring</a> •
  <a href="#ai-powered-mock-tests">Mock Tests</a> •
  <a href="#examination-analytics">Analytics</a> •
  <a href="#key-features">Features</a> •
  <a href="#architecture">Architecture</a> •
  <a href="#installation">Installation</a>
</p>

## Blockchain-Verified Credentials

ExamFlow's cutting-edge blockchain integration ensures absolute credential authenticity through immutable NFT certificates that **cannot be forged or tampered with**.

### Tamper-Proof Verification

- **Ethereum-Backed Authenticity**: Each certificate is minted as a unique non-fungible token on the Ethereum blockchain, creating a permanent, public record that can never be altered or deleted
- **Cryptographic Proof**: Digital signatures and hash functions establish mathematical certainty of certificate legitimacy
- **Decentralized Storage**: Certificate data and images stored on IPFS (InterPlanetary File System) ensuring no single point of failure or tampering opportunity
- **Public Verification**: Anyone can instantly verify credential authenticity without special software or institutional access

### Comprehensive Certificate Security

| Security Layer | Implementation | Benefit |
|----------------|----------------|---------|
| Blockchain Record | Ethereum Sepolia Network | Immutable timestamped proof that cannot be altered |
| Decentralized Storage | IPFS via Pinata | Tamper-proof file storage without centralized vulnerability |
| Smart Contract | ERC-721 NFT Standard | Cryptographic ownership with transfer capabilities |
| Verification Portal | Public blockchain explorer | Universal accessibility for verification |
| Digital Signature | Institutional cryptographic keys | Authorized issuance verification |

### Immediate Benefits

- **Eliminates Certificate Forgery**: Makes falsifying academic credentials mathematically impossible
- **Streamlines Verification**: Employers and institutions can instantly verify authenticity
- **Permanent Record**: Credentials persist indefinitely on the blockchain, immune to institutional system changes
- **Student Ownership**: Students truly own their credentials as blockchain assets

## Integrity Scoring

ExamFlow's revolutionary Integrity Scoring system provides objective, transparent measurement of examination conduct, combining academic performance with ethical behavior metrics.

### Real-Time Integrity Evaluation

- **100-Point Scale**: Students begin with a perfect integrity score that dynamically adjusts based on detected behaviors
- **Transparent Monitoring**: Real-time integrity score displayed during examination, providing immediate feedback
- **Permanent Record**: Final integrity scores permanently recorded alongside academic performance
- **Blockchain Integration**: Integrity metrics embedded in certificate NFTs for permanent ethical behavior verification

### Sophisticated Classification System

| Category | Score Range | Interpretation | Action |
|----------|-------------|----------------|--------|
| Exemplary | 90-100 | Perfect examination conduct | Normal certificate issuance |
| Good | 75-89 | Minor issues but acceptable behavior | Standard certificate with note |
| At-Risk | 50-74 | Concerning patterns requiring review | Certificate with integrity warning |
| Violation | 0-49 | Significant evidence of academic dishonesty | Potential examination invalidation |

### Comprehensive Violation Detection

ExamFlow's integrity scoring accounts for various violation types with escalating penalties:

- **Tab Switching**: 3-15 point penalties based on frequency
- **Window Focus Loss**: 2-8 point penalties per occurrence
- **Combined Violations**: Severe 10-20 point penalties for pattern violations
- **Threshold Enforcement**: Automatic examination termination when integrity falls below critical thresholds

## Advanced Proctoring

ExamFlow employs state-of-the-art proctoring technology that balances rigorous security with student privacy, ensuring examination integrity without invasive surveillance.

### Multi-Layered Detection System

- **Browser Activity Monitoring**: Advanced event tracking detects suspicious browser behaviors
- **Focus Analysis**: Sophisticated algorithms identify attention shifts and external resource usage
- **Temporal Pattern Recognition**: AI-powered identification of statistically suspicious behavioral patterns
- **Full-Screen Enforcement**: Automatic detection of examination environment manipulation

### Proctor Dashboard

Instructors gain access to a powerful real-time monitoring system:

- **Live Violation Alerts**: Instant notification of suspicious activities
- **Student Monitoring Panel**: Overview of all examination participants with integrity status
- **Detailed Logs**: Comprehensive timestamped record of all detected behaviors
- **Statistical Analysis**: Institutional patterns and trends in academic integrity
- **Evidence Repository**: Secure storage of all integrity-related data for review

### Privacy-Conscious Design

- **Data Minimization**: Only essential behavioral metrics collected without invasive video/audio
- **Transparent Monitoring**: Students fully informed of all monitored behaviors
- **Local Processing**: Primary behavior analysis performed in browser to minimize data transmission
- **Secure Protocols**: Industry-standard encryption for all integrity data
- **Ethical Design**: Balances academic integrity with student dignity and privacy

## AI-Powered Mock Tests

ExamFlow revolutionizes exam preparation with intelligent mock tests that adapt to student needs, providing realistic practice environments with personalized learning experiences.

### Intelligent Test Generation

- **Automatic Creation**: AI-powered system generates multiple versions of practice exams from question banks
- **Content Balancing**: Smart distribution of question types, topics, and difficulty levels
- **Real Environment Simulation**: Mock tests mirror the actual exam experience, including timing and interface
- **Varied Scenarios**: Multiple unique mock exams for each assessment preventing memorization

### Adaptive Learning

- **Performance Analysis**: Sophisticated algorithms identify knowledge gaps and strengths
- **Progress Tracking**: Detailed statistics across multiple practice attempts
- **Weak Area Identification**: Automatic detection of concepts requiring additional study
- **Confidence Building**: Graduated difficulty progression as mastery improves

### Comprehensive Preparation

- **Unlimited Practice**: Students can attempt mock exams multiple times
- **Instant Feedback**: Immediate scoring with detailed answer explanations
- **Strategic Insights**: Performance metrics for optimization of study strategies
- **Authentic Experience**: Full proctoring environment simulation for reduced test anxiety

## Examination Analytics

ExamFlow's analytical engine transforms examination data into actionable insights, helping instructors understand and optimize educational outcomes through visual data representation.

### Question-Level Analysis

- **Performance Metrics**: Detailed statistics for each question showing response distribution
- **Difficulty Assessment**: Automatic calculation of question difficulty based on response patterns
- **Distractor Analysis**: Effectiveness evaluation of incorrect options
- **Visual Representation**: Intuitive graphical display of response patterns

### Exam-Wide Insights

- **Performance Distribution**: Visual representation of score distribution across student population
- **Time Analysis**: Metrics on time spent per question and section
- **Comparative Statistics**: Historical performance trends across different cohorts
- **Integrity Correlation**: Relationship between integrity scores and academic performance

### Data Visualization

- **Interactive Charts**: Dynamic visualizations of examination performance data
- **Custom Filtering**: Segmentation analysis by student demographics or question characteristics
- **Exportable Reports**: Comprehensive analytics for institutional assessment
- **Real-Time Updates**: Live data processing as students complete examinations

## Key Features

### Administration Panel

- **Comprehensive Dashboard**: Real-time examination statistics with performance metrics
- **Advanced Exam Creation**: Intuitive interface with flexible configuration options
- **Secure Question Repository**: Organized question bank with categorization
- **Instant Grading**: Automated assessment with customizable scoring parameters
- **Integrity Management**: Complete violation reporting and monitoring
- **Communication Center**: Centralized notification and messaging system

### Student Experience

- **Intuitive Interface**: Clean, distraction-free examination environment
- **Progress Monitoring**: Real-time completion tracking and navigation
- **Immediate Results**: Instant scoring with detailed performance feedback
- **Certificate Generation**: One-click blockchain credential creation
- **Personal Analytics**: Individual performance insights and comparison metrics
- **Preparation Tools**: Access to AI-generated practice exams and resources

## Architecture

```
┌─────────────────────────┐      ┌────────────────────┐      ┌──────────────────────┐
│                         │      │                    │      │                      │
│   ADMINISTRATION PANEL  │◄────►│      DATABASE      │◄────►│    STUDENT PORTAL    │
│                         │      │                    │      │                      │
└─────────────────────────┘      └────────────────────┘      └──────────────────────┘
           │                               │                            │
           │                               │                            │
           ▼                               ▼                            ▼
┌─────────────────────────┐      ┌────────────────────┐      ┌──────────────────────┐
│                         │      │                    │      │                      │
│   EXAMINATION ANALYTICS │◄────►│   PROCTORING SYSTEM│◄────►│ BLOCKCHAIN INTEGRATION│
│                         │      │                    │      │                      │
└─────────────────────────┘      └────────────────────┘      └──────────────────────┘
                                         │                            │
                                         │                    ┌───────┴───────┐
                                         │                    ▼               ▼
                                         │             ┌─────────────┐ ┌─────────────┐
                                         │             │    IPFS     │ │  ETHEREUM   │
                                         │             │   STORAGE   │ │   NETWORK   │
                                         │             └─────────────┘ └─────────────┘
                                         ▼
                                ┌────────────────────┐
                                │                    │
                                │   AI MOCK EXAMS    │
                                │                    │
                                └────────────────────┘
```

## Installation

### Prerequisites

- Web server with PHP 7.4+
- MySQL 5.7+ database
- Node.js 14+ for blockchain features
- Composer for PHP dependencies
- Pinata API key for IPFS storage

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

3. **Set up required tables**

   ```bash
   # Set up analytics tables
   php setup_analytics_db.php
   
   # Set up mock exam system
   php install_mock_tables.php
   ```

4. **Configure environment**

   ```bash
   # Copy example configuration
   cp config.example.php config.php

   # Edit configuration with your database details
   nano config.php
   ```

5. **Set up blockchain integration**

   ```bash
   # Create environment file
   cp students/.env.example students/.env

   # Add your Pinata API keys and Ethereum credentials
   nano students/.env
   ```

6. **Deploy to web server**
   ```bash
   # Ensure correct permissions
   chmod -R 755 .
   chmod -R 777 uploads/
   ```

## Security Features

- **Blockchain Verification**: Tamper-proof credential verification
- **Multi-Factor Authentication**: Enhanced login security
- **Advanced Proctoring**: Sophisticated integrity monitoring
- **Secure Sessions**: Protected session management
- **Data Encryption**: Protection of sensitive information
- **Input Validation**: Prevention of injection attacks

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Developed by Team Cerebro
- Special thanks to all contributors and testers

---

<p align="center">
  <b>ExamFlow: Redefining Academic Assessment With Blockchain Integrity</b><br>
  Made with precision and innovation by Team Cerebro
</p>
