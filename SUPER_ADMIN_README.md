# Super Admin Hospital Management System

This document describes the new Super Admin functionality that has been added to the Blood Bank Management System.

## Overview

The system now supports multi-tenancy where each hospital operates as a separate tenant with isolated data. A Super Admin can create and manage multiple hospitals, each with their own users and data.

## Super Admin Access

### Default Credentials
- **Email**: `superadmin@example.com`
- **Password**: `ChangeMe123!`

**Important**: Change this password immediately after first login for security.

## Features

### 1. Super Admin Dashboard
- **Route**: `/super-admin/dashboard`
- **Features**:
  - Total hospitals count
  - Active vs inactive hospitals
  - Hospitals by city distribution
  - Quick actions for hospital management

### 2. Hospital Management
- **Route**: `/super-admin/hospitals`
- **Features**:
  - List all hospitals with filtering by city and status
  - Create new hospitals
  - Edit existing hospitals
  - Reset hospital admin passwords
  - View hospital statistics (departments, users)

### 3. Tenant Context Switching
- **Feature**: Super Admins can switch between hospital contexts
- **Usage**: Use the hospital switcher in the top navigation bar
- **Purpose**: Allows Super Admins to view and manage data within specific hospital contexts

## Hospital Creation Process

When creating a new hospital:

1. **Hospital Information**:
   - Name, City, Address, Phone, Email, Status

2. **Initial Admin User**:
   - Name, Email, Phone
   - Role: Hospital Admin
   - Default Password: `ChangeMe123!`

3. **Credentials Display**:
   - After creation, credentials are displayed
   - Admin should change password on first login

## Data Isolation

- Each hospital's data is completely isolated
- Users can only access data from their assigned hospital
- Super Admins can view all data but cannot mix data between hospitals
- Global scopes automatically filter queries by `hospital_id`

## Role System

### Super Admin
- Can create/manage hospitals
- Can switch between hospital contexts
- Has access to system-wide statistics
- Cannot access individual hospital data directly

### Hospital Admin
- Manages users within their hospital
- Has access to all hospital data
- Cannot access other hospitals

### Other Roles
- Doctor, Nurse, Reception, Lab, Finance, Read-Only
- All scoped to their assigned hospital

## Security Features

- Role-based access control (RBAC)
- Tenant isolation via middleware
- Global scopes on all tenant models
- Session-based tenant context for Super Admins

## Database Changes

### New Tables/Columns
- `hospitals` table with `city` and `status` columns
- `users` table with `hospital_id`, `status`, `last_login`
- All tenant tables now have `hospital_id` foreign key

### Models Updated
- `Hospital`, `User`, `Donor`, `Patient`, `BloodBag`, `BloodRequest`, `LabTest`, `BloodInventory`
- All tenant models use `BelongsToHospital` trait

## Usage Instructions

### For Super Admins

1. **Login** with Super Admin credentials
2. **Access Super Admin menu** from navigation
3. **Create hospitals** as needed
4. **Switch contexts** to view specific hospital data
5. **Manage hospitals** (edit, deactivate, reset admin passwords)

### For Hospital Admins

1. **Login** with provided credentials
2. **Change password** immediately
3. **Create users** for their hospital
4. **Manage hospital data** (donors, patients, blood bags, etc.)

## Technical Implementation

### Middleware
- `TenantMiddleware`: Handles tenant context resolution
- Automatically sets `tenantId` for all requests

### Traits
- `BelongsToHospital`: Applies global scopes and auto-assigns `hospital_id`

### Controllers
- `SuperAdminController`: Handles Super Admin operations
- All existing controllers now respect tenant isolation

## Troubleshooting

### Common Issues

1. **"Tenant context not set" error**:
   - Ensure user has `hospital_id` set
   - Check if `TenantMiddleware` is registered

2. **Data not showing**:
   - Verify tenant context is set correctly
   - Check if models use `BelongsToHospital` trait

3. **Permission denied**:
   - Verify user role and permissions
   - Check if user belongs to correct hospital

### Debug Commands

```bash
# Check current tenant context
php artisan tinker --execute="echo 'Tenant ID: ' . app('tenantId', 'Not set');"

# Check user hospital assignment
php artisan tinker --execute="echo 'User Hospital: ' . auth()->user()->hospital_id;"

# List all hospitals
php artisan tinker --execute="\App\Models\Hospital::all()->pluck('name', 'id');"
```

## Future Enhancements

- Hospital-specific configuration settings
- Advanced reporting across hospitals
- Bulk operations for hospital management
- Audit logging for all operations
- API endpoints for external integrations 