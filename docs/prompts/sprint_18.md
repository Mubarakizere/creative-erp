# Sprint 18

# Real-Time Collaboration & Announcement Center

Version: 1.0

Status: Planned

Priority: High

---

# Objective

Transform Creative ERP into a real-time collaborative platform.

Build a centralized Announcement Center and prepare the ERP for live collaboration using Laravel's broadcasting architecture.

This sprint extends Sprint 17.

Do NOT replace the Notification Center.

Announcements and Notifications are different concepts.

---

# Announcement Center

Create a complete Announcement module.

Announcements are organization-wide communications.

Examples:

- Company News
- HR Updates
- Scheduled Maintenance
- New Policies
- Holiday Notices
- Emergency Alerts
- Feature Releases
- System Updates

---

# Announcement Types

Support:

Information

Success

Warning

Critical

Maintenance

HR

Finance

IT

Security

General

---

# Target Audience

Announcements may target:

Entire System

Specific Company

Specific Branch

Specific Department

Specific Role

Selected Users

Future Dynamic Groups

---

# Scheduling

Announcements support:

Publish Immediately

Schedule Publish

Schedule Expiration

Draft

Archived

Expired

---

# Priority

Low

Normal

High

Critical

Pinned

---

# Services

Create:

AnnouncementService

Responsibilities:

Create

Update

Delete

Archive

Publish

Schedule

Expire

Pin

Unpin

Target Audience

Visibility

---

# Policies

Create:

AnnouncementPolicy

Permission driven.

Only authorized users may:

Create

Edit

Publish

Delete

Pin

Archive

View

---

# Permissions

Add:

announcement.view

announcement.create

announcement.update

announcement.delete

announcement.publish

announcement.pin

announcement.archive

announcement.manage

Update RolesAndPermissionsSeeder.

---

# Routes

AnnouncementController

Resource routes

Additional routes:

publish

archive

pin

unpin

schedule

expire

---

# Views

Create:

Announcements Index

Create

Edit

Show

Archive

Drafts

Scheduled

Pinned

Responsive layout.

---

# Dashboard

Integrate announcements.

Display:

Pinned announcements

Latest announcements

Critical announcements

Unread announcements

Announcement widget

---

# Sidebar

Add:

Announcements

Permission-aware.

---

# Header

Show:

Pinned announcement indicator.

Critical announcement banner when appropriate.

---

# Notification Integration

Publishing an announcement should generate notifications.

Reuse NotificationService.

Do NOT duplicate notification logic.

---

# Metrics

Extend MetricsService.

Provide:

Announcement cards

Announcement widgets

Announcement reports

Announcement charts

---

# Activity Logs

Publishing

Editing

Archiving

Deleting

Pinning

All activities should be logged.

---

# Security

Respect Architecture Enhancement 02.

Announcements must respect:

Permissions

Company

Branch

Department

Target audience

---

# Database

Support:

Title

Content

Summary

Priority

Category

Audience

Publish At

Expire At

Pinned

Published By

Metadata

Soft Deletes

---

# Search

Support:

Title

Content

Category

Priority

Date

Author

Audience

---

# Performance

Caching

Pagination

Lazy loading

Prevent N+1 queries.

---

# Stop

Do NOT implement broadcasting yet.

Prepare architecture only.

Broadcasting belongs to Part 2.
---

# Real-Time Collaboration

## Objective

Prepare the ERP for live collaboration.

The architecture must support:

- Laravel Reverb
- Laravel Echo
- Pusher
- Ably
- Redis Broadcasting

Do NOT hardcode any provider.

Broadcasting must be configurable.

---

# Broadcasting Architecture

Create a dedicated layer.

Services:

RealtimeService

BroadcastService

PresenceService

These services should abstract Laravel broadcasting.

Controllers and business modules must never broadcast events directly.

---

# Live Notifications

Extend NotificationService.

Support broadcasting notification events.

Events:

NotificationCreated

NotificationUpdated

NotificationDeleted

NotificationRead

NotificationUnread

Do NOT change Notification Center architecture.

Simply extend it.

---

# Live Announcement Updates

When an announcement is:

Published

Pinned

Archived

Expired

Broadcast updates automatically.

Dashboard should refresh without page reload.

---

# Presence System

Create online presence architecture.

Track:

Online

Offline

Away

Busy

Last Seen

Current Device

Session Count

Current Company

Current Branch

Future:

Typing

Screen Sharing

Voice Presence

---

# Presence Service

Create:

PresenceService

Responsibilities:

User Connected

User Disconnected

Heartbeat

Update Last Seen

Current Status

Online Users

Department Presence

Company Presence

---

# Live Dashboard

Dashboard widgets should support live refresh.

Examples:

Projects

Tasks

Meetings

Workflow

Notifications

Announcements

Discussions

Time Tracking

Do NOT poll aggressively.

Prepare for broadcast updates.

---

# Live Discussion Updates

Existing Discussions module should support:

New Comment

Reply

Pinned Comment

Mention

Deleted Comment

Update discussion component automatically.

---

# Live Workflow Updates

Approval requests should update automatically.

Examples:

Pending Approval

Approved

Rejected

Returned

Managers should immediately see new approval requests.

---

# Live Time Tracking

Managers should see:

Running timers

Submitted entries

Approved entries

Rejected entries

without refreshing.

---

# Live Project Updates

Broadcast:

Project Created

Project Updated

Project Archived

Project Closed

Progress Updated

---

# Live Task Updates

Broadcast:

Task Created

Task Assigned

Task Updated

Task Completed

Task Reopened

Task Archived

---

# Live Meeting Updates

Broadcast:

Meeting Scheduled

Meeting Updated

Meeting Cancelled

Meeting Started

Meeting Ended

---

# Live Metrics

MetricsService should expose broadcast-friendly refresh methods.

Do NOT duplicate calculations.

Reuse MetricsService.

---

# Dashboard Auto Refresh

Dashboard cards should update automatically.

Examples:

Unread Notifications

Pending Approvals

Active Projects

Tasks Due Today

Online Users

Announcements

---

# Toast Notifications

Create reusable component.

<x-toast>

Supports:

Success

Warning

Error

Info

Auto hide

Manual close

Queue multiple toasts

Responsive

Accessible

---

# Global Event Bus

Create a frontend event bus.

Purpose:

Update components without page reload.

Support:

Alpine.js

Future Vue

Future Livewire

Future Inertia

Avoid tight coupling.

---

# User Presence Widget

Dashboard widget:

Online Users

Grouped by:

Company

Branch

Department

Role

Future:

Recently Active

---

# Activity Feed

Activity feed should update automatically.

New:

Comments

Approvals

Announcements

Projects

Meetings

Tasks

Documents

Notifications

---

# Notification Bell

Notification bell should update automatically.

Unread badge

Dropdown

New notification animation

Mark Read

No refresh required.

---

# Performance

Never broadcast unnecessary events.

Queue broadcasts.

Respect cache.

Respect MetricsService.

Prevent duplicate broadcasts.

---

# Configuration

Create config:

config/realtime.php

Store:

Driver

Heartbeat

Presence timeout

Broadcast queues

Toast settings

Refresh intervals

Future providers

---

# Feature Flags

Create feature flags.

Enable/Disable:

Realtime

Presence

Broadcasting

Announcements

Toast

Live Dashboard

Future Collaboration

Configuration driven.

---

# Security

Respect:

Architecture Enhancement 02

Never broadcast unauthorized data.

Broadcast only to authorized channels.

Use private channels.

Use presence channels.

Verify authorization callbacks.

---

# Testing

Generate tests for:

BroadcastService

RealtimeService

PresenceService

Announcement Broadcasting

Notification Broadcasting

Private Channels

Presence Channels

Authorization

Feature Flags

Dashboard Updates

Discussion Updates

Workflow Updates

---

# Manual Verification

Verify:

✓ Announcement publishing triggers notifications

✓ Toast component works

✓ Presence tracking functions

✓ Dashboard updates

✓ Notification badge refreshes

✓ Workflow updates

✓ Discussions update

✓ Metrics remain accurate

✓ No duplicated broadcasts

✓ Queue processing works

✓ Feature flags work

✓ Existing functionality remains intact

---

# Future Ready

Prepare architecture for:

Laravel Reverb

Redis

Pusher

Ably

Web Push

Firebase

Mobile Apps

Desktop Apps

Public API

Microservices

Do NOT implement these integrations now.

Only prepare the architecture.

---

# Success Criteria

Sprint 18 is complete only if:

✓ Announcement Center complete

✓ Realtime architecture implemented

✓ Presence architecture ready

✓ Toast system implemented

✓ Live dashboard prepared

✓ Metrics integrated

✓ Notification Center extended

✓ Feature flags implemented

✓ Broadcasting abstraction complete

✓ Tests passing

✓ No regressions

✓ Enterprise architecture maintained

---

# Stop

Stop after Sprint 18.

Wait for Sprint 19.

Do NOT implement:

- Chat/Messaging
- Video Meetings
- Whiteboards
- AI Assistant
- Mobile Push

Those belong to future sprints.