<template>
  <table
    v-if="getResourcesForProps.length > 0"
    class="table w-full"
    cellpadding="0"
    cellspacing="0"
    data-testid="resource-table"
  >
    <thead>
      <tr>
        <!-- Select Checkbox -->
        <th
          :class="{
				    'w-16': shouldShowCheckboxes,
					'w-8': !shouldShowCheckboxes,
				  }"
        >&nbsp;</th>

        <!-- Field Names -->
        <th v-for="field in fields" :class="`text-${field.textAlign}`">
          <sortable-icon
            @sort="requestOrderByChange(field)"
            :resource-name="resourceName"
            :uri-key="field.attribute"
            v-if="field.sortable"
          >{{ field.indexName }}</sortable-icon>

          <span v-else>{{ field.indexName }}</span>
        </th>

        <th>
          &nbsp;
          <!-- View, Edit, Delete -->
        </th>
      </tr>
    </thead>
    <draggable
      v-model="getResourcesForProps"
      tag="tbody"
      @update="updatedSortable($event)"
      handle=".handle"
    >
      <tr
        v-for="(resource, index) in getResourcesForProps"
        :testId="`${resourceName}-items-${index}`"
        :key="resource.id.value"
        :delete-resource="deleteResource"
        :restore-resource="restoreResource"
        :disabled-sort="disabledSort"
        is="resource-table-row"
        :resource="resource"
        :resource-name="resourceName"
        :relationship-type="relationshipType"
        :via-relationship="viaRelationship"
        :via-resource="viaResource"
        :via-resource-id="viaResourceId"
        :via-many-to-many="viaManyToMany"
        :checked="selectedResources.indexOf(resource) > -1"
        :actions-are-available="actionsAreAvailable"
        :should-show-checkboxes="shouldShowCheckboxes"
        :update-selection-status="updateSelectionStatus"
      />
    </draggable>
  </table>
</template>

<script>
import { InteractsWithResourceInformation } from "laravel-nova";

export default {
  mixins: [InteractsWithResourceInformation],

  props: {
    authorizedToRelate: {
      type: Boolean,
      required: true
    },
    resourceName: {
      default: null
    },
    resources: {
      default: []
    },
    singularName: {
      type: String,
      required: true
    },
    selectedResources: {
      default: []
    },
    selectedResourceIds: {},
    shouldShowCheckboxes: {
      type: Boolean,
      default: false
    },
    actionsAreAvailable: {
      type: Boolean,
      default: false
    },
    viaResource: {
      default: null
    },
    viaResourceId: {
      default: null
    },
    viaRelationship: {
      default: null
    },
    relationshipType: {
      default: null
    },
    updateSelectionStatus: {
      type: Function
    }
  },

  data() {
    return {
      selectAllResources: false,
      selectAllMatching: false,
      getResourcesForProps: this.resources,
      resourceCount: null,
      disabledSort: false
    };
  },

  watch: {
    resources(newVal, oldValue) {
      if (newVal !== oldValue) {
        this.getResourcesForProps = this.resources;
      }
    }
  },

  methods: {
    /**
     * Delete the given resource.
     */
    deleteResource(resource) {
      this.$emit("delete", [resource]);
    },

    /**
     * Restore the given resource.
     */
    restoreResource(resource) {
      this.$emit("restore", [resource]);
    },

    /**
     * Broadcast that the ordering should be updated.
     */
    requestOrderByChange(field) {
      this.$emit("order", field);
    },

    async updatedSortable(event) {
      // this.disabledSort = true;

      // yuckkkkk
      let pagination = this.$parent.$parent.$parent.$parent;

      //   console.log("updatedSortable", "event.newIndex", event.newIndex);
      //   console.log("pagination.currentPage", pagination.currentPage);
      //   console.log("pagination.perPage", pagination.perPage);

      window._event = event;
      window._pagination = pagination;

      let data = {
        id: event.item.__vue__.resource.sort_id,
        index: event.newIndex,
        page: pagination.currentPage,
        sort_on: event.item.__vue__.resource.sort_on,
        viaRelationship: this.viaRelationship,
        viaResource: this.viaResource,
        viaResourceId: this.viaResourceId
      };

      try {
        const response = await Nova.request().post(
          `/nova-vendor/nova-sortable/${this.resourceName}/sortable`,
          data
        );

        this.$toasted.show(this.__("The new order has been set!"), {
          type: "success"
        });

        this.disabledSort = false;
        // this.$router.go(this.$router.currentRoute)
      } catch (e) {
        this.$toasted.show(
          this.__("An error occured while trying to reorder the resource."),
          { type: "error" }
        );
      } finally {
        // statements
      }
    }
  },

  computed: {
    /**
     * Get all of the available fields for the resources.
     */
    fields() {
      if (this.getResourcesForProps) {
        return this.getResourcesForProps[0].fields;
      }
    },

    /**
     * Determine if the current resource listing is via a many-to-many relationship.
     */
    viaManyToMany() {
      return (
        this.relationshipType == "belongsToMany" ||
        this.relationshipType == "morphToMany"
      );
    },

    /**
     * Determine if the current resource listing is via a has-one relationship.
     */
    viaHasOne() {
      return (
        this.relationshipType == "hasOne" || this.relationshipType == "morphOne"
      );
    }
  }
};
</script>
