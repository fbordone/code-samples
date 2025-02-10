# LibCal Block Sample

## Overview

This repository is a code sample demonstrating a custom Gutenberg block that integrates with [LibCal](https://www.springshare.com/libcal/) – an event management and scheduling platform widely used by libraries - to dynamically display upcoming events in a compact format.

The sample highlights modern WordPress development practices, including server-side rendering, API integration, and modular code organization.

## Purpose

The code sample focuses on the following aspects:

- **Custom Gutenberg Block:**
  Implementation of a block that displays LibCal event data within the WordPress editor and on the front-end.

- **API Integration:**
  A dedicated PHP class (`LibCal.php`) handles authentication, caching, API requests, and data formatting for events from LibCal.

- **Modern Web Development:**
  Demonstration of contemporary JavaScript (React) and PHP practices, including proper type declarations, error handling, and inline documentation.

## My Contributions

- **Architecture & Code Organization:**
   Spearheaded the overall functional architecture, ensuring a clear separation of concerns between the block’s editor interface, server-side rendering, and API integration.

- **Gutenberg Block Development:**
  Developed key components such as block configuration (`block.json`), editor controls (`edit.js`), block registration (`index.js`), and server-rendered markup (`markup.php`).

- **API Integration:**
  Created the `LibCal.php` class to manage API authentication, token caching, and event data formatting.

- **Styling:**
  The front-end styles (`style.css`) were primarily developed by a dedicated frontend engineer; I was responsible for code reviewing and testing to ensure consistency with our block’s design standards.

- **Documentation & Best Practices:**
  Maintained thorough inline documentation and adhered to modern coding standards, ensuring the code is both readable and maintainable.

## Suggested Files for Review

For a focused review, please take a look at the following files:

- **`libcal-block/block.json`**
  Contains block metadata and attribute definitions.

- **`libcal-block/edit.js`**
  Implements the block's editor interface and inspector controls using React.

- **`libcal-block/markup.php`**
  Provides the server-side rendering logic for the block, including integration with the LibCal API.

- **`libcal-block/style.css`**
Contains the front-end styles for the block.

- **`includes/LibCal.php`**
  Demonstrates the API integration, including token fetching, caching, and event data formatting.

## Additional Information

- **LibCal Overview:**
  LibCal is an event management platform that allows libraries to manage and display event data. This integration demonstrates how event data can be fetched and displayed dynamically in WordPress.

- **Stubbed API Responses:**
  For demo purposes, the LibCal API integration includes stubbed responses to simulate event data without requiring full API access or proprietary credentials.

- **Collaboration Note:**
  Although this sample was extracted from a larger collaborative project, the code in this repository was primarily developed and maintained by me, with contributions from a dedicated frontend engineer on the styling aspects.

## Screen Recordings

### Block Editor Experience
[Watch via Google Drive](https://drive.google.com/file/d/1u0YcXA6BczZDy-wzB9ll10zBBQT3Y2I8/view?usp=drive_link)  

### Frontend Experience
[Watch via Google Drive](https://drive.google.com/file/d/1i5fg5qKQYWWJBusyPsgQN2B-ADRkp3mx/view?usp=drive_link)

