# Backend UI Example Plugin

Compatible with upcoming OJS 3.5 release candidate. For version 3.4 check the `stable-3_4_0` branch.

## Check list to set up build step in your plugin

The tooling follows what the official Vue scaffolding ([create-vue](https://github.com/vuejs/create-vue)) generates: Vite 8 for building, ESLint (flat config) for linting and Prettier for formatting.

- Install vite@8 and the vue plugin - `npm install --save-dev vite@8 @vitejs/plugin-vue`
- Copy over the `i18nExtractKeys.vite.js` to make easy to use translation in the vue.js components
- Copy over `vite.config.js`, adjust if necessary
- Copy over relevant `scripts`, `devDependencies` and `engines` from the `package.json` to your package.json
  - `npm run build` - production build
  - `npm run dev` - rebuild on every change
  - `npm run lint` - eslint with auto fixes
  - `npm run format` - prettier
- Linting and formatting are optional, but if you want them copy over `eslint.config.js`, `.prettierrc.json` and `.prettierignore` as well. Keep the `.prettierrc.json` even if empty - it stops Prettier from picking up the config of the OJS/OMP/OPS installation your plugin lives in.
- Create `resources/js/main.js`, which will be entry point to register your components
- Check out `register` function in `BackendUiExamplePlugin.php` to see how to register the new JS and CSS file.

Node.js `^20.19.0 || ^22.13.0 || >=24.0.0` is required (see `engines` in `package.json`).

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

### How to add vue.js components to the Submission wizard

Check out `addToSubmissionWizardSteps` function in BackendUiExamplePlugin. (Support for 3.5 yet to come, follow https://github.com/pkp/pkp-lib/issues/12041)

Also check out generic plugin template https://github.com/pkp/pluginTemplate to see how the cypress test can be written to automatically test your plugin.

![image illustrating plugin example ui](docs/plugin_ui.png)
