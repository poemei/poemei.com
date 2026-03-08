#Stn-Labz, LLC. Development Doctrine
**Project**: MVC Project

***Authority**: Stn-Labz, LLC.

**Status**: Active

**Version**: 1.1.0

**Last Updated**: 2026-03-07

**Applies To**: The Stn-Labz MVC Projects

## I. Corporate Oversight & Ownership
All development, architecture, and deployment of the Indicia Institute system are governed strictly by Stn-Labz, LLC.. No architectural deviations are permitted without explicit review and approval from the managing entity.

## II. AI Integrity & Anti-Hallucination Clause
At no time is an Artificial Intelligence (AI) or automated agent permitted to:
 - Generate internal requirements or project specifications outside of those explicitly defined in this Doctrine.
 - Initiate unauthorized writes to the database or modify schema architecture independently.
 - Override manual directives or established coding standards regarding file naming or class structure.
 - Implement "clever" logic that bypasses the manual, deliberate module registration process.

Any AI-generated code must be strictly audited to ensure it contains no "hallucinated" functionality or autonomous database logic that contradicts the directives of Stn-Labz, LLC.

## III. Architectural Principle
The MVC Project is a strictly controlled MVC system.
 - It is not a CMS.
 - It is not a plugin host.
 - It is not dynamically extensible.

## IV. Core Structural Rule
Every module must contain exactly:
**Controller**: `/app/controllers/{module}.php`
**Model**: `/app/models/{module}_model.php`
**Public Views**: `/app/views/public/{module}/*.php`
**Admin Views**: `/app/views/admin/`
**Naming Standard**: All file names, classes, and functions must remain entirely lowercase without exception.

## V. Data Governance
No HTML is to be stored in the database.
 - Rendering is governed by the MVC Rendering Grammar (v1.0.8+).

**Persistence**: Nothing is deleted; data may only be archived or deactivated.
