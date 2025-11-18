import BuiExampleTab from "./Components/BuiExampleTab.vue";
import BuiMyComponentWithDialog from "./Components/BuiMyComponentWithDialog.vue";
import BuiFileManagerCellIthenticate from "./Components/BuiFileManagerCellIthenticate.vue";
import BuiPublicationListing from "./Components/BuiPublicationListing.vue";
import BuiSubmissionWizardExample from "./Components/BuiSubmissionWizardExample.vue";
import BuiSubmissionWizardReviewExample from "./Components/BuiSubmissionWizardReviewExample.vue";

import { computed, watch } from "vue";
pkp.registry.registerComponent("BuiPublicationListing", BuiPublicationListing);
pkp.registry.registerComponent("BuiExampleTab", BuiExampleTab);
pkp.registry.registerComponent(
  "BuiMyComponentWithDialog",
  BuiMyComponentWithDialog
);
pkp.registry.registerComponent("BuiPublicationListing", BuiPublicationListing);
pkp.registry.registerComponent(
  "BuiFileManagerCellIthenticate",
  BuiFileManagerCellIthenticate
);
pkp.registry.registerComponent(
  "BuiSubmissionWizardExample",
  BuiSubmissionWizardExample
);
pkp.registry.registerComponent(
  "BuiSubmissionWizardReviewExample",
  BuiSubmissionWizardReviewExample
);

// File manager extensions
pkp.registry.storeExtend("fileManager_SUBMISSION_FILES", (piniaContext) => {
  // Enable this only for Editorial dashboard, not for author
  const dashboardStore = pkp.registry.getPiniaStore("dashboard");
  if (dashboardStore.dashboardPage !== "editorialDashboard") {
    return;
  }

  const { useUrl } = pkp.modules.useUrl;
  const { useFetch } = pkp.modules.useFetch;

  const fileStore = piniaContext.store;

  // build query params reactively based on current files
  const ithenticateQueryParams = computed(() => {
    const fileIds = fileStore?.files?.map((file) => file.id) || [];

    return { fileIds };
  });

  const { apiUrl } = useUrl(`submissions/ithenticate`);

  const { fetch: fetchIthenticateStatus, data: ithenticateStatus } = useFetch(
    apiUrl,
    {
      query: ithenticateQueryParams,
    }
  );

  // fetch the ithenticate status when the fileIds changes
  watch(ithenticateQueryParams, (newQueryParams) => {
    if (newQueryParams?.fileIds?.length) {
      fetchIthenticateStatus();
    }
  });

  // attach the state to the store, so its possible to retrieve it for example in the components that are injected via JS Hooks
  fileStore.ithenticateStatus = ithenticateStatus;

  fileStore.extender.extendFn("getColumns", (columns, args) => {
    // adding new column last to the end
    console.log("extending getColumn");
    const newColumns = [...columns];

    const { useLocalize } = pkp.modules.useLocalize;
    const { t } = useLocalize();

    console.log("columns:");
    console.log(columns);

    // to get prop passed to FileManager
    console.log("submission:");
    console.log(fileStore.props.submission);

    // to get anything thats not available in args/props, but its available on particular store.
    console.log("files:");
    console.log(fileStore.files);

    newColumns.splice(newColumns.length - 1, 0, {
      // header label of new column
      header: t("plugins.generic.backendUiExample.ithenticate"),
      // component responsible for rendering that table cell
      component: "BuiFileManagerCellIthenticate",
      props: {},
    });

    return newColumns;
  });

  fileStore.extender.extendFn("getItemActions", (originalResult, args) => {
    console.log("getItemActions");
    console.log("file:");
    console.log(args.file);

    console.log("submission from props:");
    console.log(fileStore.props.submission);

    if (args.file) {
      console.log(
        "ithenticate status:",
        ithenticateStatus.value?.[args.file.id] || null
      );
    }
    return [
      ...originalResult,
      {
        label: "custom action",
        name: "buiCustomAction",
        icon: "Globe",
        actionFn: ({ file }) => {
          const { useModal } = pkp.modules.useModal;
          const { useLocalize } = pkp.modules.useLocalize;

          const { openDialog } = useModal();
          const { localize, localizeSubmission } = useLocalize();

          const workflowStore = pkp.registry.getPiniaStore("workflow");

          openDialog({
            title: "Custom action on file",
            message: `Do you want to make custom action on file:${localize(
              file.name
            )}. With submission title ${localizeSubmission(
              workflowStore.submission.publications[0].fullTitle,
              workflowStore.submission.locale
            )}`,
            actions: [
              {
                label: "Yes",
                isPrimary: true,
                callback: (close) => {
                  close();
                  workflowStore.workflowViewLibrary();
                  // user has confirmed
                },
              },
              {
                label: "No",
                isWarnable: true,
                callback: (close) => {
                  close();
                  // user has cancelled. close the modal
                },
              },
            ],
          });
        },
      },
    ];
  });
});

pkp.registry.storeExtend("workflow", (piniaContext) => {
  const workflowStore = piniaContext.store;

  // Extending workflow menu
  workflowStore.extender.extendFn("getMenuItems", (menuItems, args) => {
    const updatedMenuItems = [
      ...menuItems,
      {
        key: "buiCustomMenu",
        label: "Custom menu",
        state: { primaryMenuItem: "buiCustomMenu" },
      },
    ];

    return updatedMenuItems;
  });

  // Render custom component in existing menu
  workflowStore.extender.extendFn("getPrimaryItems", (primaryItems, args) => {
    if (
      args?.selectedMenuState?.primaryMenuItem === "workflow" &&
      args?.selectedMenuState?.stageId ===
        pkp.const.WORKFLOW_STAGE_ID_SUBMISSION
    ) {
      return [
        ...primaryItems,
        {
          component: "BuiPublicationListing",
          props: { submission: args.submission },
        },
      ];
    } else {
      return primaryItems;
    }
  });

  // Render custom component in custom menu
  workflowStore.extender.extendFn("getPrimaryItems", (primaryItems, args) => {
    if (args?.selectedMenuState?.primaryMenuItem === "buiCustomMenu") {
      return [
        {
          component: "BuiPublicationListing",
          props: { submission: args.submission },
        },
      ];
    }
    return primaryItems;
  });
});
