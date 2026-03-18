# Software Requirements Specification

**Project:** ToolShed — Tool Rental Management Platform
**Document ID:** SRS-TOOLSHED-001
**Version:** 1.1
**Date:** 2026-03-03

---

## Table of Contents

1. [Introduction](#1-introduction)
2. [Overall Description](#2-overall-description)
3. [System Architecture](#3-system-architecture)
4. [Data Model](#4-data-model)
5. [Functional Requirements](#5-functional-requirements)
6. [Non-Functional Requirements](#6-non-functional-requirements)
7. [Constraints and Assumptions](#7-constraints-and-assumptions)

---

## 1. Introduction

### 1.1 Purpose

This document specifies the functional and non-functional requirements for the ToolShed platform. It is intended for use by developers, testers, and project stakeholders as the authoritative statement of what the system shall do.

### 1.2 Scope

ToolShed is a web application through which authenticated users discover, reserve, and manage rentals of physical tools from a network of depot locations. The system manages the full rental lifecycle from catalogue browsing through tool return, including pricing calculation, status tracking, and audit logging.

### 1.3 Definitions

| Term | Definition |
|------|------------|
| Tool | A physical item available for rental, identified by a unique SKU. |
| Booking | A reservation of a tool between a start date and an end date. |
| Depot | A physical location where tools are stored, collected, and returned. |
| ToolStatus | The current state of a tool: `available`, `reserved`, `out`, or `archived`. |
| BookingStatus | The current state of a booking: `pending`, `confirmed`, `active`, `returned`, or `cancelled`. |
| AuditLog | An immutable record of a significant system event. |
| PriceBreakdown | A value object holding subtotal, discount amount, tax amount, tax rate, maintenance fee, and grand total for a booking. The tax rate is resolved from the depot at which the tool is located. |
| Renter | An authenticated, email-verified user who creates and manages bookings. |
| Staff | A privileged user scoped to a depot who manages inventory and advances booking states. |
| Admin | A user with full platform management access across all depots. |

---

## 2. Overall Description

### 2.1 Product Overview

ToolShed allows renters to browse a catalogue of tools, filter by location and category, and make date-range bookings. Bookings follow a defined state machine from creation through physical return. Pricing is calculated from the tool's daily rate, applicable discounts, a depot-level tax rate, and a per-tool maintenance fee. All state-changing operations are recorded in an immutable audit log.

### 2.2 User Classes

| Class | Description | Authentication |
|-------|-------------|----------------|
| Guest | Unauthenticated visitor. May view the welcome page only. | None |
| Renter | Registered user. May browse the catalogue, create bookings, and manage their own rental history. | Required + email verified |
| Staff | Depot-level operator. May manage tool inventory and advance booking states for their assigned depot. | Required + role |
| Admin | Platform administrator. Full access to all system functions across all depots. | Required + role |

### 2.3 Assumptions

1. Booking dates are treated as calendar dates with no time component. All date arithmetic uses UTC.
2. A tool has a single currency denomination matching its depot. Cross-currency conversion is out of scope for the current version.
3. Physical tool availability is managed manually by depot staff. No IoT or barcode integration is assumed.
4. Tool images are referenced by external URL. File upload and local storage are not required in the current version.
5. Payment processing is not required in the current version. Pricing is calculated and displayed but no payment gateway is integrated.
6. Email delivery requires a configured SMTP or API driver in production environments.

---

## 3. System Architecture

### 3.1 Technology Stack

| Layer | Technology |
|-------|------------|
| Application framework | Laravel 12, PHP 8.2 |
| Frontend runtime | Livewire 4, wire:navigate SPA mode |
| UI component library | Flux UI v2.9 |
| CSS framework | Tailwind CSS v4 via @tailwindcss/vite |
| Build tool | Vite |
| Authentication | Laravel Fortify |
| Database (development) | SQLite |
| Database (production) | MySQL or PostgreSQL |
| Test framework | Pest 3 |

### 3.2 Routes

| Route | Component | Description |
|-------|-----------|-------------|
| `GET /` | Static view | Welcome / marketing page |
| `GET /dashboard` | Static view | Authenticated user dashboard |
| `GET /tools` | ToolGallery | Browsable, filterable tool catalogue |
| `GET /tools/{tool}` | BookingDatePicker | Individual tool booking form |
| `GET /bookings` | BookingList | Current user's booking history |
| `GET /depots` | DepotFinder | Proximity search and preferences |
| `GET /settings/profile` | Livewire page | Profile settings |
| `GET /settings/password` | Livewire page | Password change |
| `GET /settings/two-factor` | Livewire page | TOTP two-factor management |
| `GET /settings/appearance` | Livewire page | Theme/appearance preferences |

---

## 4. Data Model

### 4.1 Entity Summary

```
User ─────────────────── Booking ──────── Tool ──────── Depot
                                           |
                                       AuditLog (polymorphic)
```

### 4.2 users

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| name | string | |
| email | string unique | |
| birth_year | integer nullable | Set at registration; cannot be changed. |
| password | string | Bcrypt hashed. |
| role | enum | `renter`, `staff`, or `admin`. Default: `renter`. |
| preferred_currency | string(3) | ISO 4217. Default: USD. |
| city | string nullable | Saved from depot finder. |
| country_code | string(2) nullable | ISO 3166-1 alpha-2. |
| latitude | float nullable | Saved from depot finder. |
| longitude | float nullable | Saved from depot finder. |
| two_factor_secret | text nullable | TOTP secret, encrypted. |
| two_factor_recovery_codes | text nullable | Encrypted. |
| email_verified_at | datetime nullable | |

### 4.3 tools

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| sku | string unique | Human-readable asset identifier. |
| name | string | |
| description | text nullable | |
| image_url | string nullable | External image URL. |
| category | string nullable | e.g. Power, Access, Concrete. |
| daily_rate_cents | integer | Minor currency units. |
| maintenance_fee_cents | integer | Fixed per-booking charge. |
| currency_code | string(3) | ISO 4217. |
| status | enum | ToolStatus cast. |
| user_id | bigint FK | Owning user. |
| depot_id | bigint FK nullable | Hosting depot. |

### 4.4 bookings

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| tool_id | bigint FK | |
| user_id | bigint FK | |
| start_date | date | Immutable after creation. |
| end_date | date | Immutable after creation. |
| booking_status | string | See BookingStatus definition. |

### 4.5 depots

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| name | string | |
| address_line1 | string | |
| address_line2 | string nullable | |
| city | string | |
| state_province | string nullable | |
| postal_code | string nullable | |
| country_code | string(2) | ISO 3166-1 alpha-2. |
| currency_code | string(3) | ISO 4217. |
| tax_rate | decimal | Jurisdiction VAT/tax rate as a decimal fraction. Default `0.15` (Namibia). |
| latitude | float | Used for Haversine proximity calculation. |
| longitude | float | |
| phone | string nullable | |
| email | string nullable | |
| is_active | boolean | |

### 4.6 audit_logs

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id | bigint nullable | The user who performed the action. |
| ip_address | string nullable | Originating request IP. |
| timestamp | datetime | UTC. Set at creation; never updated. |
| action | string | e.g. `booking.confirmed`, `booking.returned`. |
| subject_type | string | Polymorphic model class. |
| subject_id | bigint | Polymorphic model ID. |

---

## 5. Functional Requirements

---

### FR-0 — Roles and Access Control

**FR-0.1** The system shall define four user roles: `guest`, `renter`, `staff`, and `admin`. Every authenticated user shall be assigned exactly one role. The default role assigned at registration shall be `renter`.

**FR-0.2** The `users` table shall include a `role` column as an enum restricted to the values `renter`, `staff`, and `admin`. Unauthenticated access is represented by the absence of a session and is not stored as a role value.

**FR-0.3** The system shall enforce role-based access control via dedicated middleware applied to all protected routes. A request from a user whose role does not satisfy the route's minimum required role shall be rejected with HTTP 403. The response shall not disclose the existence of the resource to unauthorised callers.

**FR-0.4** The following permission matrix shall govern access to system capabilities:

| Capability | Guest | Renter | Staff | Admin |
|---|---|---|---|---|
| View public catalogue | Yes | Yes | Yes | Yes |
| Create a booking | No | Yes | Yes | Yes |
| View own bookings | No | Yes | Yes | Yes |
| Return own active booking | No | Yes | Yes | Yes |
| View all bookings (any user) | No | No | Yes | Yes |
| Advance booking state (confirm, dispatch, return) | No | No | Yes | Yes |
| Cancel any booking | No | No | Yes | Yes |
| Create and edit tools | No | No | No | Yes |
| Archive tools | No | No | No | Yes |
| Create and edit depots | No | No | No | Yes |
| Deactivate depots | No | No | No | Yes |
| View audit log | No | No | Yes | Yes |
| Manage user roles | No | No | No | Yes |
| View damage reports | No | No | Yes | Yes |
| Accept or reject damage reports | No | No | Yes | Yes |

**FR-0.5** Role assignment and role changes shall be performed exclusively by an `admin` user through the admin console. A user shall not be able to modify their own role.

**FR-0.6** Every role change shall produce an audit log entry recording the admin who made the change, the affected user, the previous role, and the new role.

**FR-0.7** The `staff` role shall be scoped to a depot. A staff user shall only be able to manage tools, bookings, and damage reports belonging to their assigned depot. An `admin` user shall have access across all depots.

**FR-0.8** The admin console routes shall be served under a dedicated path prefix (e.g. `/admin`) and shall be protected by both the authentication middleware and a role-check middleware. A request to an admin route by a `renter` or `guest` shall return HTTP 403, not a redirect to the login page.

---

### FR-1 — Authentication

**FR-1.1** The system shall allow a visitor to register an account by providing a name, email address, birth year, and password. The email address must be unique across all accounts.

**FR-1.2** The system shall validate birth year as a four-digit integer between (current year - 120) and the current year. Birth year shall not be modifiable after registration.

**FR-1.3** The system shall send an email verification link upon successful registration. Access to authenticated routes other than profile settings shall require a verified email address.

**FR-1.4** The system shall authenticate users by email address and password. A persistent session shall be established on success. The session shall be fully invalidated on logout.

**FR-1.5** The system shall allow users to enrol a TOTP authenticator application for two-factor authentication. Once enrolled, every login attempt shall require a valid current OTP in addition to the password. The system shall generate and display one-time recovery codes at enrolment.

**FR-1.6** The system shall provide a password-reset flow: the user supplies their email address, receives a time-limited reset link, and sets a new password via that link.

**FR-1.7** An authenticated user shall be able to update their name and email address. A change to email address shall invalidate the verification status and trigger a new verification email.

---

### FR-2 — Tool Catalogue

**FR-2.1** The system shall display all tools whose status is not `archived` in a paginated grid.

**FR-2.2** The catalogue shall support the following filters, each bound to the URL query string so that filtered views are bookmarkable and shareable:
- Full-text search against tool name and SKU.
- Filter by depot ID.
- Filter by category.

**FR-2.3** Each tool in the catalogue shall display: name, SKU, category, current status, daily rate, and an image. When no image URL is present, a per-category colour gradient and category identifier shall be shown.

**FR-2.4** The system shall provide a tool detail view that displays the full tool description, pricing breakdown, depot name and address, and a booking entry point, without navigating away from the catalogue.

**FR-2.5** The catalogue shall support sorting by daily rate (ascending and descending) and by distance from the user's saved location.

**FR-2.6** Tools shall be filterable by a maximum daily rate value.

---

### FR-3 — Booking

**FR-3.1** A renter shall be able to create a booking for any tool with status `available` by selecting a start date and end date. The start date must be today or later. The end date must be after the start date.

**FR-3.2** The booking form shall validate the date range in real time on every change to either date field. An error message shall be displayed immediately when the range is invalid. The submission control shall be disabled while any validation error is present.

**FR-3.3** Booking creation and the corresponding tool status transition shall execute within a single database transaction. If either operation fails, both shall be rolled back.

**FR-3.4** Upon successful creation, a booking shall be assigned status `pending` then immediately transitioned to `confirmed`, and the associated tool's status shall be set to `reserved`.

**FR-3.5** The submission control shall display a loading indicator and be disabled for the duration of the server round-trip to prevent duplicate submission.

**FR-3.6** A renter shall be able to view all of their own bookings, including tool name, SKU, date range, duration, and current booking status.

**FR-3.7** A renter shall be able to mark an `active` booking as returned. This shall transition the booking to `returned` and the tool to `available`.

**FR-3.8** The system shall prevent a booking from being created for dates that overlap with an existing confirmed or active booking for the same tool.

**FR-3.9** A renter shall be able to cancel a booking that is in `confirmed` status, provided the start date is more than 48 hours in the future.

**FR-3.10** Staff shall be able to advance a booking through its states (confirm, dispatch, return) and cancel a booking with a mandatory reason.

---

### FR-4 — Booking State Machine

**FR-4.1** Bookings shall follow a defined state machine. The permitted transitions are:

| Transition | From status | To status | Tool status after |
|------------|-------------|-----------|-------------------|
| confirm | pending | confirmed | reserved |
| dispatch | confirmed | active | out |
| return | active | returned | available |

**FR-4.2** Any transition not listed above shall be rejected. An attempt to perform an illegal transition shall raise an application exception and leave both the booking and tool state unchanged.

**FR-4.3** The `dispatch` transition shall additionally require that the booking's start date has been reached. Attempting to dispatch before the start date shall be rejected.

---

### FR-5 — Pricing

**FR-5.1** The system shall calculate a price breakdown for a booking using the following formula:

```
subtotal        = daily_rate_cents x days
discounted_base = subtotal x (1 - discount_rate)
tax             = discounted_base x tax_rate
total           = discounted_base + tax + maintenance_fee_cents
```

`tax_rate` is the value stored on the depot from which the tool is rented (see FR-5.7).

**FR-5.2** The standard weekly discount rate shall be 10%, applied to bookings of 7 or more days.

**FR-5.3** The combined discount rate applied to any single booking shall not exceed 25% of the subtotal, regardless of how many discount rules are composed.

**FR-5.4** All monetary values shall be stored as integer minor-currency units (cents). Display formatting shall always render with the correct symbol and decimal precision for the tool's currency.

**FR-5.5** The booking form shall display a live price breakdown — subtotal, discount, tax, maintenance fee, and total — that updates as the user modifies the date range.

**FR-5.6** The system shall support displaying prices in the user's preferred currency, converting from the tool's native currency using an exchange rate fetched from an external provider.

**FR-5.7** Each depot record shall store a `tax_rate` value expressed as a decimal fraction (e.g. `0.15` for 15%). The default value for new depots shall be `0.15`, reflecting the VAT rate applicable in Namibia. When a depot operates in a jurisdiction with a different statutory rate, an admin shall be able to set a distinct `tax_rate` on that depot. The price breakdown shall use the `tax_rate` of the depot associated with the booking's tool, not a system-wide constant.

---

### FR-6 — Depot Finder

**FR-6.1** The system shall accept a latitude, longitude, and search radius in kilometres, and return all active depots within that radius, sorted by distance ascending.

**FR-6.2** Distance shall be calculated using the Haversine formula.

**FR-6.3** The depot list shall be filterable by ISO 3166-1 alpha-2 country code.

**FR-6.4** The system shall provide a set of preset city coordinates that can be applied to the location fields with a single interaction.

**FR-6.5** A user shall be able to save their preferred currency, city, and country code to their profile from the depot finder. Saved values shall be pre-populated on subsequent visits.

**FR-6.6** The system shall accept a browser geolocation request and use the returned coordinates to pre-populate the latitude and longitude fields.

---

### FR-7 — Audit Logging

**FR-7.1** The system shall write an audit log entry for every booking state transition. Each entry shall record: the authenticated user's ID, the originating IP address, the UTC timestamp, the action identifier, and a polymorphic reference to the subject model.

**FR-7.2** Audit log records shall be immutable. No update or delete operations shall be permitted on audit log entries.

**FR-7.3** Staff and admin users shall be able to view a paginated, filterable audit log. Filters shall include: user, action type, subject type, and date range.

---

### FR-8 — Tool Management

**FR-8.1** A tool's status may be set to `archived`. Archived tools shall not appear in the catalogue and shall not be available for booking. All historical booking and audit data for the tool shall be retained.

**FR-8.2** Admin users shall be able to create, edit, and archive tools through a management interface.

**FR-8.3** Tool records shall support the following additional fields: serial number, condition (new, good, fair, poor), last serviced date, weight, and dimensions.

**FR-8.4** The system shall flag tools whose last serviced date is more than 90 days in the past.

**FR-8.5** Admin users shall be able to import tools in bulk via a CSV file of up to 500 rows.

---

### FR-9 — Depot Management

**FR-9.1** Admin users shall be able to create, edit, deactivate, and reactivate depots.

**FR-9.2** Deactivating a depot shall hide all of its tools from the catalogue and prevent new bookings against those tools. Existing active bookings shall not be affected.

---

### FR-10 — Notifications

**FR-10.1** The system shall send a transactional email to the renter when a booking is confirmed. The email shall include tool name, SKU, depot address, start date, end date, and total price.

**FR-10.2** The system shall dispatch a reminder notification to the renter 48 hours before the booking end date.

**FR-10.3** If a booking remains in `active` status more than 24 hours past its end date, the system shall notify the renter and the depot.

**FR-10.4** An in-application notification centre shall display unread notifications to the authenticated user.

---

### FR-11 — Reviews

**FR-11.1** After a booking transitions to `returned`, the renter shall be prompted once to submit a rating from 1 to 5 and an optional written review for the tool.

**FR-11.2** The tool detail view shall display the average rating and the count of reviews. The five most recent visible reviews shall be listed.

**FR-11.3** Staff shall be able to hide individual reviews. Hidden reviews shall be excluded from public display but retained in the database.

---

### FR-12 — Waitlist and Availability

**FR-12.1** When a tool is `reserved` or `out`, an authenticated user shall be able to add themselves to a waitlist for that tool. The system shall notify waitlisted users in sign-up order when the tool becomes `available`.

**FR-12.2** The tool detail view shall display a monthly calendar marking booked date ranges, available dates, and the current date.

**FR-12.3** The booking date pickers shall prevent selection of date ranges that overlap with existing confirmed or active bookings for the same tool.

---

### FR-13 — Damage Reporting

**FR-13.1** When initiating a return, the renter shall be required to declare the condition of the tool: undamaged, minor damage, or major damage. A written description shall be required for any non-undamaged selection.

**FR-13.2** Staff shall be able to review submitted damage reports and accept, reject, or escalate them.

**FR-13.3** An accepted damage report shall generate a damage charge record linked to the booking, specifying amount, currency, and description.

---

### FR-14 — Payments

**FR-14.1** At booking confirmation, the renter shall complete payment via an integrated payment provider. The booking shall remain in `pending` status until payment is confirmed via webhook callback, at which point it shall transition to `confirmed`.

**FR-14.2** A cancellation submitted more than 48 hours before the start date shall issue a full refund. A cancellation within 48 hours of the start date shall forfeit the first day's rental rate.

**FR-14.3** A PDF invoice shall be generated for every confirmed booking and attached to the confirmation email.

---

### FR-15 — API

**FR-15.1** The system shall expose a versioned REST API at `/api/v1/` authenticated via bearer tokens (Laravel Sanctum).

**FR-15.2** The API shall provide the following endpoints:

| Endpoint | Method | Description |
|----------|--------|-------------|
| /api/v1/tools | GET | Paginated tool list with search and filter support. |
| /api/v1/tools/{tool} | GET | Single tool record. |
| /api/v1/depots | GET | All active depots. |
| /api/v1/depots/{depot}/tools | GET | Tools at a specific depot. |
| /api/v1/bookings | GET | The authenticated user's bookings. |
| /api/v1/bookings | POST | Create a booking. |
| /api/v1/bookings/{booking} | PATCH | Advance a booking's status. |

**FR-15.3** The system shall support configurable outbound webhooks that fire on `booking.confirmed`, `booking.returned`, and `tool.archived` events.

---

### FR-16 — Organisations

**FR-16.1** Users shall be able to belong to an organisation. An organisation may have a private tool catalogue scoped to its members.

**FR-16.2** Bookings made under an organisation shall be aggregated into a monthly invoice.

**FR-16.3** An organisation administrator role shall be able to manage members, view the organisation's booking history and invoices, and configure a credit limit.

---

## 6. Non-Functional Requirements

**NFR-01** All state-mutating operations on bookings and tool status shall execute within a single database transaction. A failure at any step shall roll back all changes made within that transaction.

**NFR-02** All user-supplied input shall be validated server-side before any database operation is performed. Client-side validation, where present, is supplementary only.

**NFR-03** All forms that mutate state shall include CSRF protection.

**NFR-04** All user-facing strings shall be wrapped in the Laravel `__()` translation helper to support future localisation.

**NFR-05** Error inputs shall carry `aria-invalid` attributes. Keyboard navigation shall be functional throughout the application.

**NFR-06** The tool catalogue grid shall render in a minimum of two columns on small viewports and up to four columns on extra-large viewports.

**NFR-07** The gallery page shall return a response within 500 ms under normal load conditions.

**NFR-08** Livewire component re-renders triggered by search or filter changes shall complete within 300 ms on a properly provisioned server.

**NFR-09** The application shall support 1,000 concurrent authenticated sessions without degradation to the response-time targets above.

**NFR-10** Passwords shall be stored using bcrypt. No plaintext or reversibly encoded passwords shall be stored.

**NFR-11** Two-factor secrets shall be stored encrypted at rest.

**NFR-12** All HTTP communication shall be over TLS in production environments.

**NFR-13** The codebase shall pass Laravel Pint formatting checks with no violations.

**NFR-14** All service-layer classes (PricingCalculator, ToolStatusTransitioner, AuditLogger, DepotProximityService) shall have unit test coverage of no less than 85% of their branching paths.

**NFR-15** The audit log shall be retained for a minimum of 7 years. No automated purge process shall be applied to audit log records.

---

## 7. Constraints and Assumptions

1. The system is built on Laravel 12 with Livewire 4. No alternative framework is in scope.
2. SQLite is used in development and test environments. Production deployments require MySQL 8+ or PostgreSQL 15+.
3. Tool images are externally hosted. The system stores only the URL; image upload and CDN management are outside the current scope.
4. No payment gateway is integrated in the current version. All pricing calculations are informational only.
5. Booking dates carry no time-of-day component. The system does not model depot opening hours or intra-day availability.
6. A tool belongs to at most one depot at any given time. Tool transfers between depots are not modelled.
7. All monetary values are stored in the minor unit of the tool's native currency. The system does not perform currency conversion in the current version.
8. Email delivery in production requires a configured transport driver. The application does not manage email infrastructure.

