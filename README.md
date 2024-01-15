# TSN Central Officer Records -- Website

This repository contains the code for the website displaying the TSN COR.

It is based on previous work by @astrolamb-gaming

Work on this codebase is continued in https://github.com/tsnrp/tsncor-website -- this repository is archived.

## Configuration

Some basic configuration can be done in `configuration.js` as well as `api/configuration.php`, where we just define a few variables that determine the behaviour of the app. 
If you're here because the COR spreadsheet told you to "update the AppsScript deployment URL in the website", then you'll want to check out `api/configuration.php`.
You'll want to change the `TSNCOR_DEPLOYMENT_WEB_APP_URL` variable.


## Code structure

This is a relatively plain "hand-crafted" website with some HTML and CSS, and a little PHP.
You'll find the main structure in `index.html`, and the biggest part of the logic in `modules/store.js`.

There's a very small bit of logic which runs server-side.
This you'll find in the `api` directory.
This part is responsible for caching the spreadsheet data, which is necessary since getting the data from the spreadsheet is quite slow.
It also caches the award ribbon pictures. This is needed for the images to work. Google made a change in early 2024 that made it no longer possible to get the award images directly.

### Client

The code uses petite-vue (based on vue.js) to introduce some reactive patterns.
If this is something you've never heard of, here's the TLDR:
The key point is that we're displaying dynamic (changing) data in our HTML.
The petite-vue library takes care of updating the HTML whenever the data changes.
If you've ever done something like this "by hand", you'll know what a pain it can be.

The basic principle is this: In `store.js`, we define all of the dynamic data that we might want to display.
We also define all the ways in which the data can be changed.
If you open `modules/store.js`, you'll see some data fields, and a lot of functions that filter or modify the data.

Then, in `index.html`, we put some directives for petite-vue that tell it where the data needs to go.
It works kind of like a template: if you write `{{ officer.name }}`, then that whole part will be automatically replaced with the officer's name (as defined in `store.js`).
You can look at `index.html` to see it in action.

There are some more cool features. The important ones are:
* the `v-if` directive can be added to an HTML tag to make this tag only visible if a javascript condition is true
* the `v-for` directive can be added to an HTML tag. This will make petite-vue repeat the tag once for each item in a javascript list.
* the `v-bind` directive can be added to inputs (like the search field). This means: "whenever this input field changes, update that javascript variable, and vice versa".

### API (caching)

There are a few PHP files in `api`. These are responsible for server side caching.

The data we display comes from two different sources: The Apps Script (this is attached to the COR spreadsheet and gives us the spreadsheet data) and the Google Drive (This is where the pictures for award ribbons are).

It takes quite long to retrieve data from Google, so we have a cache: we first load the data from Google onto the TSN server, and then from the TSN server to the client.

The `api/cache_function.php` file contains the main logic for fetching a cached file, making sure it isn't expired, and sending it to the client. This function is used by all other PHP scripts.

In `api/appsscript.php` we accept an appsscript query and return the (cached) result from appsscript.

In `api/images.php` we accept a query for an image file, and get the file from Google Drive.

### Look & Feel

If you want to change the look of the page, you'll want to look at `layout.css` and `style.css`.
The first one takes care of arranging everything correctly.
The second one is for defining colors, text size, font, and so on.
