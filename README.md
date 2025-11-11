# Backend UI Example Plugin

Compatible with upcoming OJS 3.5 release candidate. For version 3.4 check the `stable-3_4_0` branch.

## Check list to set up build step in your plugin

- Install vite@6 - `npm install --save-dev vite@6`
- Copy over the i18nExtractKeys.vite.js to make easy to use translation in the vue.js components
- Copy over vite.config.js, adjust if necessary
- You can follow the eslint/prettier setup - but thats very much optional
- Copy over relevant scripts from the `package.json` to your package.json
- Create `resources/js/main.js`, which will be entry point to register your components
- Check out `register` function in `BackendUiExamplePlugin.inc.php` to see how to register the new JS and CSS file.

## Scenarios illustrated in this example plugin

### How to inject your own vue component to smarty template

Additional tab is injected in Settings -> Website -> Setting Example tab

### How to use components from ui-library

Components in ui-library are globally available with `pkp` prefix. Check for example `BuiPublicationListing.vue`, which is leveraging table component.

### How to use dialog and sideModal via useModal composable

Check out `BuiMyComponentWithDialog.vue` to see example.

Another useful example is in main.js, with new custom action for file manager, which also open dialog.

### How to do API data fetching, using useFetch and useUrl composables

Check out `BuiExampleTab.vue`, where interacting with API using `useFetch` and `useUrl` is illustrated.

### How to extend FileManager with additional column and custom action and fetch additional data

Checkout `main.js` to see how to add custom column or action. Fetching additional data for displayed files is also demonstrated..

### How to add new menu on workflow page with custom content

Checkout `main.js`, particularly extending `getMenuItems` and `getPrimaryItems`.

Also check out generic plugin template https://github.com/pkp/pluginTemplate to see how the cypress test can be written to automatically test your plugin.

![image illustrating plugin example ui](docs/plugin_ui.png)
