# Comprehensive Product Requirements Document (PRD)
# BukidnonAlert: Web-Based Incident Reporting and Vehicle Utilization System for MDRRMO

## Executive Summary

BukidnonAlert is a centralized web-based incident reporting and vehicle utilization management system designed specifically for the Municipal Disaster Risk Reduction and Management Office (MDRRMO) of Bukidnon Province. The system serves as a unified platform for emergency response coordination, incident management, and resource optimization across multiple municipalities.

## Project Overview

### Application Name
**BukidnonAlert**

### Primary Purpose
A comprehensive emergency management platform that centralizes incident reporting, vehicle fleet management, and analytics for enhanced disaster response coordination across Bukidnon municipalities.

### Technology Stack
- **Backend Framework**: Laravel 12 (PHP 8.2+) with MVC (Model-View-Controller)
- **Frontend**: blade template with minimal javascript
- **Styling**: Tailwind CSS 4.0 with DaisyUi
- **Database**: PostgreSQL (Centralized)
- **Build Tool**: Vite 6.2.4
- **Authentication**: Laravel Session-based

---

## System Architecture

### Centralized Database Approach
- **Single PostgreSQL Database**: Serves all municipalities
- **Data Isolation**: Municipality-based data separation
- **Centralized Administration**: Admin controls user creation and municipality assignment
- **Data Integrity**: Prevents cross-municipality data modification

### User Role Hierarchy

#### 1. Admin (System-wide Access)
- **Full System Control**: Complete access to all features
- **User Management**: Can create and manage all user types
- **Municipality Assignment**: Sets user municipality affiliations
- **System Configuration**: Manages system-wide settings

#### 2. MDRRMO Staff (Municipality-specific)
- **Incident Management**: Create, update, and manage incidents
- **Vehicle Operations**: Track and manage vehicle fleet
- **Request Processing**: Approve/reject citizen requests
- **Analytics Access**: View municipality-specific analytics

#### 3. Responders (Mobile-first)
- **Mobile Incident Reporting**: Field incident reporting via mobile devices
- **Location Services**: GPS-based incident location capture
- **Photo Upload**: Capture and upload incident photos
- **Real-time Updates**: Live status updates from field

#### 4. Citizens/Victims (Public Interface)
- **Request Submission**: Submit incident report requests
- **Status Tracking**: Monitor request processing status
- **Report Download**: Access approved incident reports

---

## Core Features & Modules

### 1. Incident Management System

#### Incident Reporting
- **Multi-channel Input**: Desktop admin, mobile responder, citizen requests
- **Comprehensive Data Capture**:
  - Incident type classification
  - GPS coordinates (lat/lng)
  - Date and time tracking
  - Severity levels (Critical, High, Medium, Low)
  - Weather and road conditions
  - Vehicle involvement details
  - Casualty and injury counts
  - Property damage estimates

#### Incident Tracking
- **Unique Incident Numbers**: Auto-generated (INC-YYYY-XXX format)
- **Status Management**: Pending, Active, Resolved, Closed
- **Assignment System**: Link incidents to staff and vehicles
- **Progress Monitoring**: Real-time status updates

#### Victim Management
- **Detailed Victim Records**:
  - Personal information (name, age, gender, contact)
  - Medical status and injury classification
  - Hospital referral tracking
  - Transportation method documentation
  - Safety equipment usage (helmet, seatbelt)
  - Vehicle involvement details

### 2. Vehicle Utilization Management

#### Fleet Overview
- **Real-time Status Tracking**: Available, In Use, Maintenance, Out of Service
- **Vehicle Classification**: Ambulance, Fire Truck, Rescue Vehicle, Patrol Car
- **Registration Management**: License plates, vehicle numbers

#### Operational Metrics
- **Fuel Management**:
  - Current fuel levels (percentage display)
  - Fuel capacity tracking
  - Consumption trend analysis
- **Mileage Tracking**:
  - Odometer readings
  - Usage patterns
  - Distance-based maintenance alerts
- **Personnel Assignment**:
  - Driver assignment tracking
  - Staff allocation management

#### Maintenance System
- **Scheduled Maintenance**: Preventive maintenance calendar
- **Service History**: Complete maintenance records
- **Alert System**: Overdue maintenance notifications
- **Compliance Tracking**: Inspection and certification management

### 3. Request Management System

#### Citizen Request Processing
- **Request Categories**:
  - Official incident reports
  - Traffic accident reports
  - Medical emergency reports
  - Fire incident reports
  - General emergency reports

#### Workflow Management
- **Status Tracking**: Pending → Processing → Approved/Rejected → Completed
- **Approval Workflow**: Staff review and approval process
- **Bulk Operations**: Multi-request approval capabilities
- **Notification System**: Status update notifications

#### Request Data Capture
- **Personal Information**: Full name, contact, email, ID number, address
- **Incident Details**: Date, time, location, type, description
- **Request Specifics**: Report type, urgency level, purpose
- **Supporting Documentation**: Case numbers, reference materials

### 4. Analytics & Reporting Dashboard

#### Statistical Overview
- **Incident Metrics**:
  - Total incidents by severity
  - Monthly incident trends
  - Resolution time analysis  <!--  -->
  - Geographic distribution

#### Geographic Analytics
- **Heat Map Visualization**:
  - Incident density mapping
  - Hotspot identification
  - Color-coded severity indicators (Critical: Red, High: Orange, Medium: Yellow, Low: Green)
  - Interactive map interface with hover details

#### Municipality Comparison
- **Cross-municipality Analytics**:
  - Incident frequency comparison
  - Response time benchmarking
  - Resource utilization analysis
  - Performance metrics dashboard

#### Trend Analysis
- **Time-based Patterns**:
  - Seasonal incident trends
  - Day/time incident distribution
  - Historical data comparison
  - Predictive analytics capabilities

### 5. Mobile Responder Interface

#### Field Reporting Capabilities
- **GPS Integration**: Automatic location capture
- **Camera Functionality**: Photo capture and upload
- **Offline Mode**: Data collection without internet connectivity <!--  alert please connect to internet-->
- **Quick Report Templates**: Pre-configured incident types

#### Real-time Updates
- **Live Status Broadcasting**: Field updates to central system
- **Push Notifications**: Emergency alerts and assignments
- **Two-way Communication**: Command center coordination

---

## Technical Specifications

### Database Schema (12 Tables)

#### Core Tables
1. **users**: User authentication and role management
2. **incidents**: Central incident records
3. **vehicles**: Fleet management data
4. **victims**: Incident victim records
5. **requests**: Citizen request management

#### System Tables
6. **password_reset_tokens**: Password recovery
7. **activity_logs**: System audit trail
8. **login_attempts**: Security monitoring
9. **sessions**: User session management
10. **cache**: Performance optimization
11. **cache_locks**: Concurrency control
12. **jobs**: Background task processing

### Security Features

#### Authentication & Authorization
- **Role-based Access Control**: Granular permission system
- **Session Management**: Secure session handling
- **Password Security**: Hashed password storage
- **Account Security**: Failed login attempt tracking for audit purposes

#### Data Protection
- **Input Validation**: Comprehensive data sanitization
- **CSRF Protection**: Cross-site request forgery prevention
- **SQL Injection Prevention**: Parameterized queries
- **Audit Trail**: Complete activity logging

### Performance Optimizations

#### Database Performance
- **Strategic Indexing**: Optimized query performance
- **Composite Indexes**: Multi-column query optimization
- **Query Optimization**: Efficient data retrieval patterns

#### Caching Strategy
- **Application Caching**: Frequently accessed data caching
- **Session Caching**: User session optimization
- **Query Result Caching**: Database query optimization

---

## User Interface Design

### Design System
- **Framework**: Tailwind CSS 4.0
- **Color Scheme**: Emerald-based theme (Emergency services appropriate)
- **Typography**: Instrument Sans font family
- **Responsive Design**: Mobile-first approach

### Component Architecture
- **Reusable Components**: DRY principle implementation
- **Consistent Navigation**: Role-based sidebar navigation
- **Interactive Elements**: Hover states and transitions
- **Accessibility**: Screen reader compatible

### Mobile Optimization
- **Responsive Layouts**: Adaptive design across devices
- **Touch-friendly Interface**: Mobile gesture support
- **Offline Capability**: PWA features for field use 
- **GPS Integration**: Location-based services

---

## API Specifications

### Internal APIs
- **Real-time Data**: Live incident and vehicle status updates
- **Statistics API**: Analytics data endpoints
- **Notification API**: System notification management

### Mobile API Support
- **RESTful Endpoints**: Standard HTTP API methods
- **JSON Response Format**: Consistent data structure
- **Authentication**: Token-based mobile authentication
- **Offline Sync**: Data synchronization capabilities

---

## Deployment & Infrastructure

### Server Requirements
- **PHP**: Version 8.2 or higher
- **Database**: PostgreSQL 13+
- **Web Server**: Apache/Nginx
- **Storage**: File upload and image storage

### Scalability Considerations
- **Municipality Growth**: Support for additional municipalities
- **User Scaling**: Concurrent user support
- **Data Volume**: Large-scale incident data management
- **Performance Monitoring**: System health tracking

---

## Security & Compliance

### Data Privacy
- **Personal Data Protection**: Victim information security
- **Access Controls**: Need-to-know data access
- **Data Retention**: Appropriate data lifecycle management

### Compliance Requirements
- **Government Standards**: Local government compliance
- **Emergency Response Protocols**: Industry standard adherence
- **Audit Requirements**: Complete activity trail maintenance

---

## Future Enhancements

### Phase 2 Features
- **SMS Notifications**: Text message alert system
- **Advanced Analytics**: Machine learning incident prediction
- **Mobile App**: Native mobile application
- **Integration APIs**: Third-party system integration

### Scalability Roadmap
- **Province-wide Deployment**: Multi-province support
- **Advanced Mapping**: Enhanced GIS integration
- **IoT Integration**: Sensor data incorporation
- **AI-powered Analytics**: Predictive incident modeling

---

## Success Metrics

### Key Performance Indicators (KPIs)
- **Response Time**: Average incident response time reduction
- **System Adoption**: User engagement metrics
- **Data Accuracy**: Incident reporting precision
- **Resource Utilization**: Vehicle and staff efficiency

### Operational Metrics
- **Incident Resolution Rate**: Percentage of resolved incidents
- **User Satisfaction**: Citizen and staff feedback scores
- **System Uptime**: Service availability percentage
- **Data Completeness**: Incident record completeness rate

---

## Project Timeline & Milestones

### Current Status: 85% Complete
- ✅ **Core Infrastructure**: Database, authentication, routing
- ✅ **Basic UI Components**: Layout system, navigation, forms
- ✅ **Request Management**: Complete citizen request workflow
- ✅ **Vehicle Tracking**: Basic fleet management interface
- ✅ **Analytics Foundation**: Heat map and statistics framework

### Remaining Development
- 🚧 **Incident Management**: Complete CRUD operations
- 🚧 **Mobile Interface**: Responder mobile optimization
- 🚧 **Advanced Analytics**: Complete reporting system
- 🚧 **Testing & QA**: Comprehensive system testing
- 🚧 **Documentation**: User manuals and admin guides

### Go-Live Requirements
- **Data Migration**: Import existing incident data
- **User Training**: Staff and admin training programs
- **System Testing**: Load testing and security audit
- **Backup Systems**: Disaster recovery procedures

---

## Conclusion

BukidnonAlert represents a comprehensive solution for emergency management and vehicle utilization tracking specifically designed for the MDRRMO of Bukidnon Province. The system's centralized architecture, role-based access control, and multi-channel incident reporting capabilities provide a robust foundation for enhanced emergency response coordination.

The current implementation provides a solid foundation with 85% of core functionality complete. The remaining development focuses on advanced incident management features, mobile optimization, and comprehensive testing to ensure system reliability and user adoption.

---

**Document Version**: 1.0  
**Last Updated**: January 2025  
**Author**: System Analysis  
**Status**: Final Draft - Ready for Implementation
