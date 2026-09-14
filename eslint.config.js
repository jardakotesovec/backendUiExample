import { defineConfig, globalIgnores } from "eslint/config";
import globals from "globals";
import js from "@eslint/js";
import pluginVue from "eslint-plugin-vue";
import skipFormatting from "eslint-config-prettier/flat";

export default defineConfig([
  { files: ["**/*.{vue,js,mjs,cjs}"] },
  globalIgnores(["public/build/**", "registry/**"]),
  {
    languageOptions: {
      globals: {
        ...globals.browser,
        // global provided by OJS/OMP/OPS, exposing registry, modules, const, ...
        pkp: "readonly",
      },
    },
  },
  js.configs.recommended,
  ...pluginVue.configs["flat/essential"],
  {
    rules: {
      // unused callback parameters are kept in the examples to document what is available
      "no-unused-vars": ["error", { args: "none" }],
    },
  },
  // must be last, disables formatting related rules so they don't conflict with prettier
  skipFormatting,
]);
