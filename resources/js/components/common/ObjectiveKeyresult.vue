<template>
  <section>
    <s-accordions defaultIndex="0">
      <s-accordion v-for="(obj, index) in getUserObjectives" :key="index">
        <template #header>
          <div class="icon-left">
            <div class="icon-arch"></div>
            <div class="icon-circle"></div>
          </div>
          <div class="title">
            <h4>{{ obj.title }}</h4>
            <small
              >{{ obj.type }}
              <span v-if="obj.team">({{ obj.team }})</span></small
            >
            <small>{{ formatTime(obj.created_at) }}</small>
          </div>
        </template>
        <template #headerIcon>
          <v-menu open-on-hover bottom transition="slide-y-transition" offset-y>
            <template v-slot:activator="{ on, attrs }">
              <v-btn v-bind="attrs" v-on="on" class="icon-right" icon>
                <v-icon>mdi-dots-horizontal</v-icon>
              </v-btn>
            </template>
            <ul class="s__menu">
              <li
                class="s__menu__list"
                @click="
                  openKeyResultDrawer(
                    {
                      id: obj.id,
                      type: obj.type,
                      team: obj.pivot && obj.pivot.team_id,
                    },
                    false,
                    obj
                  )
                "
              >
                <v-icon>mdi-shape-plus</v-icon>
                <span>Add key result</span>
              </li>
              <li
                class="s__menu__list"
                @click="
                  openKeyResultDrawer(
                    {
                      id: obj.id,
                      type: obj.type,
                      team: obj.pivot && obj.pivot.team_id,
                    },
                    true
                  )
                "
              >
                <v-icon>mdi-shape-plus</v-icon>
                <span>Add & close key result</span>
              </li>
              <li class="s__menu__list" @click="setOjectiveToUpdate(obj)">
                <v-icon>mdi-pencil-outline</v-icon>
                <span>Edit Objective</span>
              </li>
              <li class="s__menu__list" @click="deleteOjective(obj.id)">
                <v-icon>mdi-delete-outline</v-icon>
                <span>Delete Objective</span>
              </li>
            </ul>
          </v-menu>
        </template>
        <template #headerContent>
          <div class="collapse__header__middle">
            <!-- <div class="avatars">
              <div class="avatars__avatar">
                <img src="" alt="av" srcset="" />
              </div>
              <div class="avatars__avatar">
                <img src="" alt="av" srcset="" />
              </div>
              <div class="avatars__avatar">
                <img src="" alt="av" srcset="" />
              </div>
            </div>
            <div class="pills">
              <div class="pills__pill">
                <span>share</span>
              </div>
              <div class="pills__pill">
                <span><v-icon>mdi-wechat</v-icon></span>
                <span>chat</span>
              </div>
            </div> -->

            <div class="collapse__header__middle__period">
              <p>
                {{
                  `${obj.period.when} ${formatDateYear(obj.period.startDate)}`
                }}
              </p>
              <span
                >{{ formatDateMonth(obj.period.startDate) }} -
                {{ formatDateMonth(obj.period.endDate) }}</span
              >
            </div>
          </div>
          <div class="collapse__header__right">
            <div class="progress">
              <v-progress-circular
                :value="obj.metric.progress"
                color="primary"
                :size="70"
                :width="10"
              >
                {{
                  `${obj.metric.progress}${
                    obj.metric.unit === "percentage" ? "%" : ""
                  }`
                }}
              </v-progress-circular>
            </div>
            <div class="fat__arrow">
              <div class="arrow"></div>
            </div>
          </div>
        </template>
        <section class="collapse__content">
          <template v-if="obj.key_results.length">
            <div
              class="collapse__content__timeline"
              v-for="(kr, indx) in obj.key_results"
              :key="indx"
            >
              <div class="line"></div>
              <div class="dot"></div>
              <div class="title">
                <span>{{ kr.metric }}</span>
                <div class="kr__actions">
                  <span
                    class="icon"
                    title="Edit key result"
                    @click="setKeyResultToUpdate(obj, kr)"
                  >
                    <v-icon>mdi-pencil-outline</v-icon>
                  </span>
                  <span
                    class="icon"
                    title="Delete key result"
                    @click="deleteKeyResult(obj.id, kr.id)"
                  >
                    <v-icon>mdi-delete-outline</v-icon>
                  </span>
                </div>
              </div>
              <div class="user">
                <div class="sperator"></div>
                <div class="details">
                  <span
                    v-if="
                      kr.user.profile.first_name && kr.user.profile.last_name
                    "
                    >{{ kr.user.profile.first_name }}
                    {{ kr.user.profile.last_name }}</span
                  >
                  <span v-else>{{ kr.user.username }}</span>
                  <div class="avatar">
                    <img
                      :src="`/storage/avatar/user/${kr.user.profile.avatar}`"
                      alt="av"
                      srcset=""
                    />
                  </div>
                </div>
                <div class="sperator"></div>
              </div>
              <div class="date">
                <span
                  >{{ formatDateMonth(kr.period.startDate) }} -
                  {{ formatDateMonth(kr.period.endDate) }}</span
                >
              </div>
              <div
                class="range"
                :id="`check-in-${getCheckInId === kr.id ? kr.id : ''}`"
                @click="openCheckIn(obj.id, kr)"
                style="display: block"
              >
                <s-range
                  :status="kr.kr_metric.status"
                  :unit="obj.metric.unit === 'percentage' ? '%' : ''"
                  :min="kr.kr_metric.start"
                  :max="kr.kr_metric.target"
                  v-model.number="obj.key_results[indx].kr_metric.progress"
                  @change="updateObjectiveProgress($event, obj.id)"
                ></s-range>
              </div>
              <div :class="['badge', kr.kr_metric.status]">
                <span v-if="kr.kr_metric.status === 'cm'">Completed</span>
                <span v-if="kr.kr_metric.status === 'ot'">On Track</span>
                <span v-else-if="kr.kr_metric.status === 'bd'">Behind</span>
                <span v-else-if="kr.kr_metric.status === 'ar'">At Risk</span>
                <span v-else-if="kr.kr_metric.status === 'ns'"
                  >Not Started</span
                >
              </div>
            </div>
          </template>
          <article class="collapse__content__no--kr" v-else>
            <span>No Key Result found!</span>
            <button
              type="button"
              class="
                v-btn v-btn--is-elevated v-btn--has-bg
                theme--light
                v-size--large
                primary
              "
              @click="
                openKeyResultDrawer(
                  {
                    id: obj.id,
                    type: obj.type,
                    team: obj.pivot && obj.pivot.team_id,
                  },
                  false,
                  obj
                )
              "
            >
              <span>Add Key Result</span>
            </button>
          </article>
        </section>
      </s-accordion>
    </s-accordions>

    <s-drawer v-model="objectiveDrawer">
      <section class="add__objective">
        <header class="add__objective__header">
          <div class="add__objective__header__title">
            <span>
              <v-icon>mdi-bullseye-arrow</v-icon>
            </span>
            <h4>
              {{ isEditingOjective ? "Edit Objective" : "New Objective" }}
            </h4>
          </div>
          <v-btn icon @click="cancelObjective()"
            ><v-icon>mdi-close</v-icon></v-btn
          >
        </header>
        <main>
          <form
            @submit.prevent="
              isEditingOjective ? updateOjective() : addNewOjective()
            "
          >
            <v-row>
              <v-col class="d-flex" cols="6" sm="6">
                <v-text-field
                  label="Title"
                  outlined
                  dense
                  v-model="objective.title"
                  :error-messages="objectiveErrors.title"
                ></v-text-field>
              </v-col>
              <v-col class="d-flex" cols="6" sm="6">
                <v-select
                  :items="[
                    { value: 'percentage', text: '%' },
                    { value: 'number', text: 'Number' },
                  ]"
                  item-value="value"
                  item-text="text"
                  label="Unit"
                  outlined
                  dense
                  v-model="objective.unit"
                  :error-messages="objectiveErrors.unit"
                ></v-select>
              </v-col>
            </v-row>
            <s-accordions>
              <s-accordion label="Details">
                <v-row align="center">
                  <v-col class="d-flex" cols="6" sm="6">
                    <v-select
                      :items="objectiveType"
                      item-value="value"
                      item-text="text"
                      label="Type"
                      dense
                      outlined
                      append-outer-icon="mdi-information-variant"
                      v-model="objective.type"
                      :error-messages="objectiveErrors.type"
                      @click:append-outer="openInputInfo('type')"
                      :id="`input-info-${
                        getInpuInfoType === `type` ? getInpuInfoType : ''
                      }`"
                      :disabled="
                        isSelectedTeam ||
                        isSelectedIndividual ||
                        isSelectedOrganization
                      "
                    >
                      <template v-slot:selection="data">
                        <div class="">
                          <v-avatar left size="25">
                            <v-icon
                              color="primary"
                              v-if="data.item.value === 'individual'"
                              >mdi-account-circle-outline</v-icon
                            >
                            <v-icon
                              color="primary"
                              v-else-if="data.item.value === 'team'"
                              >mdi-sitemap</v-icon
                            >
                            <v-icon color="primary" v-else>mdi-web</v-icon>
                          </v-avatar>
                          <span class="">{{ data.item.text }}</span>
                        </div>
                      </template>
                      <template v-slot:item="data">
                        <div class="">
                          <v-avatar left size="25">
                            <v-icon
                              color="primary"
                              v-if="data.item.value === 'individual'"
                              >mdi-account-circle-outline</v-icon
                            >
                            <v-icon
                              color="primary"
                              v-else-if="data.item.value === 'team'"
                              >mdi-sitemap</v-icon
                            >
                            <v-icon color="primary" v-else>mdi-web</v-icon>
                          </v-avatar>
                          <span class="">{{ data.item.text }}</span>
                        </div>
                      </template>
                    </v-select>
                  </v-col>
                  <v-col class="d-flex" cols="6" sm="6">
                    <v-autocomplete
                      :items="getFormattedTeams"
                      item-value="value"
                      item-text="text"
                      outlined
                      dense
                      label="Team"
                      multiple
                      append-outer-icon="mdi-information-variant"
                      v-model="objective.team"
                      :error-messages="objectiveErrors.team"
                      @click:append-outer="openInputInfo('team')"
                      :id="`input-info-${
                        getInpuInfoType === `team` ? getInpuInfoType : ''
                      }`"
                      v-if="showTeamInput"
                      :disabled="isSelectedTeam"
                    >
                      <template v-slot:selection="data">
                        <div class="">
                          <v-avatar left size="25">
                            <v-icon color="primary">mdi-sitemap</v-icon>
                          </v-avatar>
                          <span class="">{{ `Team: ${data.item.text}` }}</span>
                        </div>
                      </template>
                      <template v-slot:item="data">
                        <div class="">
                          <v-avatar left size="25">
                            <v-icon color="primary">mdi-sitemap</v-icon>
                          </v-avatar>
                          <span class="">{{ `Team: ${data.item.text}` }}</span>
                        </div>
                      </template>
                    </v-autocomplete>
                  </v-col>
                </v-row>
                <v-row align="center">
                  <v-col class="d-flex" cols="6" sm="6">
                    <v-select
                      :items="objectivePeriods"
                      item-text="text"
                      item-value="value"
                      label="When"
                      dense
                      outlined
                      append-outer-icon="mdi-information-variant"
                      :error-messages="objectiveErrors.period.when"
                      v-model="objective.period.when"
                      @click:append-outer="openInputInfo('when')"
                      :id="`input-info-${
                        getInpuInfoType === `when` ? getInpuInfoType : ''
                      }`"
                    ></v-select>
                  </v-col>
                </v-row>
                <v-row align="center">
                  <v-col class="d-flex" cols="6" sm="6">
                    <v-menu
                      ref="startDate"
                      v-model="startDateMenu"
                      :close-on-content-click="false"
                      transition="scale-transition"
                      offset-y
                      max-width="290px"
                      min-width="auto"
                      append-outer-icon="mdi-information-variant"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="startDateFormatted"
                          label="Start date"
                          v-bind="attrs"
                          v-on="on"
                          dense
                          outlined
                          append-outer-icon="mdi-information-variant"
                          :error-messages="objectiveErrors.period.startDate"
                          @click:append-outer="openInputInfo('startDate')"
                          :id="`input-info-${
                            getInpuInfoType === `startDate`
                              ? getInpuInfoType
                              : ''
                          }`"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="objective.period.startDate"
                        no-title
                        @input="startDateMenu = false"
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col class="d-flex" cols="6" sm="6">
                    <v-menu
                      ref="endDate"
                      v-model="endDateMenu"
                      :close-on-content-click="false"
                      transition="scale-transition"
                      offset-y
                      max-width="290px"
                      min-width="auto"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="endDateFormatted"
                          label="End date"
                          v-bind="attrs"
                          v-on="on"
                          dense
                          outlined
                          append-outer-icon="mdi-information-variant"
                          @click:append-outer="openInputInfo('endState')"
                          :error-messages="objectiveErrors.period.endDate"
                          :id="`input-info-${
                            getInpuInfoType === `endState`
                              ? getInpuInfoType
                              : ''
                          }`"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="objective.period.endDate"
                        no-title
                        @input="endDateMenu = false"
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                </v-row>
                <s-accordions>
                  <s-accordion
                    label="More options"
                    labelRight="No tags, No description"
                    :sub="true"
                  >
                    <v-row align="center">
                      <v-col class="d-flex" cols="6" sm="6">
                        <v-text-field
                          label="Tags"
                          outlined
                          dense
                          placeholder="eg. tag1, tag2, tag3"
                          v-model="objective.tag"
                          :error-messages="objectiveErrors.tag"
                        ></v-text-field>
                      </v-col>
                      <v-col class="d-flex" cols="6" sm="6">
                        <v-text-field
                          label="Description"
                          placeholder="Objective description"
                          outlined
                          dense
                          v-model="objective.description"
                          :error-messages="objectiveErrors.description"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </s-accordion>
                </s-accordions>
              </s-accordion>
              <v-divider class="py-2"></v-divider>
              <s-accordion label="Outcome" labelRight="Get to 100% complete">
                <p>Measured as a goal towards 100% completion</p>
              </s-accordion>
              <v-divider class="py-2"></v-divider>
              <s-accordion label="Progress" labelRight="Updated manually">
                <v-select
                  :items="progressType"
                  item-value="value"
                  item-text="text"
                  dense
                  outlined
                  append-outer-icon="mdi-information-variant"
                  v-model="objective.progress.type"
                  :error-messages="objectiveErrors.progress.type"
                  @click:append-outer="openInputInfo('progressType')"
                  :id="`input-info-${
                    getInpuInfoType === `progressType` ? getInpuInfoType : ''
                  }`"
                >
                  <template v-slot:selection="data">
                    <div class="">
                      <v-avatar left size="25">
                        <v-icon
                          color="primary"
                          v-if="data.item.value === 'manual'"
                          >mdi-account-cog-outline</v-icon
                        >
                        <v-icon color="primary" v-else
                          >mdi-arrow-right-top</v-icon
                        >
                      </v-avatar>
                      <span class="">{{ data.item.text }}</span>
                    </div>
                  </template>
                  <template v-slot:item="data">
                    <div class="">
                      <v-avatar left size="25">
                        <v-icon
                          color="primary"
                          v-if="data.item.value === 'manual'"
                          >mdi-account-cog-outline</v-icon
                        >
                        <v-icon color="primary" v-else
                          >mdi-arrow-right-top</v-icon
                        >
                      </v-avatar>
                      <span class="">{{ data.item.text }}</span>
                    </div>
                  </template>
                </v-select>
                <s-accordions>
                  <s-accordion
                    label="More options"
                    labelRight="No tags, No description"
                    :sub="true"
                  >
                    <v-select
                      :items="progressOptions"
                      outlined
                      dense
                      append-outer-icon="mdi-information-variant"
                      v-model="objective.progress.option"
                      :error-messages="objectiveErrors.progress.option"
                      @click:append-outer="openInputInfo('progressOptions')"
                      :id="`input-info-${
                        getInpuInfoType === `progressOptions`
                          ? getInpuInfoType
                          : ''
                      }`"
                    >
                      <template v-slot:selection="data">
                        <div class="">
                          <v-avatar
                            left
                            size="25"
                            color="primary"
                            v-if="objective.progress.type === 'manual'"
                          >
                            <span class="white--text text-h6">SW</span>
                          </v-avatar>
                          <v-avatar left size="25" v-else>
                            <v-icon color="primary">mdi-arrow-right-top</v-icon>
                          </v-avatar>
                          <span class="">{{ data.item.text }}</span>
                        </div>
                      </template>
                      <template v-slot:item="data">
                        <div class="">
                          <v-avatar
                            left
                            size="25"
                            color="primary"
                            v-if="objective.progress.type === 'manual'"
                          >
                            <span class="white--text text-h6">SW</span>
                          </v-avatar>
                          <v-avatar left size="25" v-else>
                            <v-icon color="primary">mdi-arrow-right-top</v-icon>
                          </v-avatar>
                          <span class="">{{ data.item.text }}</span>
                        </div>
                      </template>
                    </v-select>
                  </s-accordion>
                </s-accordions>
              </s-accordion>
              <v-divider class="py-2"></v-divider>
              <s-accordion label="Alignment" labelRight="Get to 100% complete">
                <v-btn text color="primary"
                  ><v-icon>mdi-arrow-up-left</v-icon> Align objective</v-btn
                >
              </s-accordion>
            </s-accordions>

            <div class="create-obj-btns my-6">
              <v-btn
                text
                elevation="5"
                large
                class="mr-3"
                :disabled="isSubmitting"
                @click="cancelObjective()"
                >Cancel</v-btn
              >
              <v-btn
                type="submit"
                elevation="5"
                large
                color="primary"
                :loading="isSubmitting"
                >{{ isEditingOjective ? "Save" : "Create" }}</v-btn
              >
            </div>
          </form>
        </main>
      </section>
    </s-drawer>
    <s-drawer v-model="keyResultDrawer">
      <section class="add__objective">
        <header class="add__objective__header">
          <div class="add__objective__header__title">
            <span>
              <v-icon>mdi-gauge</v-icon>
            </span>
            <h4>
              {{ isEditingKeyresult ? "Update Key Result" : "New Key Result" }}
            </h4>
          </div>
          <v-btn icon @click="cancelKeyResult()"
            ><v-icon>mdi-close</v-icon></v-btn
          >
        </header>
        <main>
          <form
            @submit.prevent="
              isEditingKeyresult ? updateKeyResult() : addNewKeyResult()
            "
          >
            <v-text-field
              label="Metric"
              outlined
              dense
              v-model="keyResult.metric"
              :error-messages="keyResultErrors.metric"
            ></v-text-field>
            <v-row align="center">
              <v-col class="d-flex" cols="6" sm="6">
                <v-text-field
                  label="Starting"
                  outlined
                  dense
                  v-model="keyResult.starting"
                  :error-messages="keyResultErrors.starting"
                ></v-text-field>
              </v-col>
              <v-col class="d-flex" cols="6" sm="6">
                <v-text-field
                  label="Target"
                  outlined
                  dense
                  v-model="keyResult.target"
                  :error-messages="keyResultErrors.target"
                ></v-text-field>
              </v-col>
            </v-row>
            <v-text-field
              label="Title"
              outlined
              dense
              v-model="keyResult.title"
              :error-messages="keyResultErrors.title"
            ></v-text-field>
            <s-accordions>
              <s-accordion label="Progress" labelRight="Updated manually">
                <v-select
                  :items="progressType1"
                  item-value="value"
                  item-text="text"
                  dense
                  outlined
                  append-outer-icon="mdi-information-variant"
                  v-model="keyResult.progress.type"
                  :error-messages="keyResultErrors.progress.type"
                  @click:append-outer="openInputInfo('progressType')"
                  :id="`input-info-${
                    getInpuInfoType === `progressType` ? getInpuInfoType : ''
                  }`"
                >
                  <template v-slot:selection="data">
                    <div class="">
                      <v-avatar left size="25">
                        <v-icon
                          color="primary"
                          v-if="data.item.value === 'manual'"
                          >mdi-account-cog-outline</v-icon
                        >
                        <v-icon color="primary" v-else
                          >mdi-arrow-right-top</v-icon
                        >
                      </v-avatar>
                      <span class="">{{ data.item.text }}</span>
                    </div>
                  </template>
                  <template v-slot:item="data">
                    <div class="">
                      <v-avatar left size="25">
                        <v-icon
                          color="primary"
                          v-if="data.item.value === 'manual'"
                          >mdi-account-cog-outline</v-icon
                        >
                        <v-icon color="primary" v-else
                          >mdi-arrow-right-top</v-icon
                        >
                      </v-avatar>
                      <span class="">{{ data.item.text }}</span>
                    </div>
                  </template>
                </v-select>
                <s-accordions>
                  <s-accordion
                    label="More options"
                    labelRight="No tags, No description"
                    :sub="true"
                  >
                    <v-select
                      :items="progressOptions1"
                      outlined
                      dense
                      append-outer-icon="mdi-information-variant"
                      v-model="keyResult.progress.option"
                      :error-messages="keyResultErrors.progress.option"
                      @click:append-outer="openInputInfo('progressOptions')"
                      :id="`input-info-${
                        getInpuInfoType === `progressOptions`
                          ? getInpuInfoType
                          : ''
                      }`"
                    >
                      <template v-slot:selection="data">
                        <div class="">
                          <v-avatar
                            left
                            size="25"
                            color="primary"
                            v-if="keyResult.progress.type === 'manual'"
                          >
                            <span class="white--text text-h6">SW</span>
                          </v-avatar>
                          <v-avatar left size="25" v-else>
                            <v-icon color="primary">mdi-arrow-right-top</v-icon>
                          </v-avatar>
                          <span class="">{{ data.item.text }}</span>
                        </div>
                      </template>
                      <template v-slot:item="data">
                        <div class="">
                          <v-avatar
                            left
                            size="25"
                            color="primary"
                            v-if="keyResult.progress.type === 'manual'"
                          >
                            <span class="white--text text-h6">SW</span>
                          </v-avatar>
                          <v-avatar left size="25" v-else>
                            <v-icon color="primary">mdi-arrow-right-top</v-icon>
                          </v-avatar>
                          <span class="">{{ data.item.text }}</span>
                        </div>
                      </template>
                    </v-select>
                  </s-accordion>
                </s-accordions>
              </s-accordion>
              <s-accordion label="Details">
                <v-row align="center">
                  <v-col class="d-flex" cols="6" sm="6">
                    <v-select
                      :items="objectiveType"
                      item-value="value"
                      item-text="text"
                      label="Type"
                      dense
                      outlined
                      append-outer-icon="mdi-information-variant"
                      v-model="keyResult.type"
                      :error-messages="keyResultErrors.type"
                      @click:append-outer="openInputInfo('type')"
                      :id="`input-info-${
                        getInpuInfoType === `type` ? getInpuInfoType : ''
                      }`"
                      disabled
                    >
                      <template v-slot:selection="data">
                        <div class="">
                          <v-avatar left size="25">
                            <v-icon
                              color="primary"
                              v-if="data.item.value === 'individual'"
                              >mdi-account-circle-outline</v-icon
                            >
                            <v-icon
                              color="primary"
                              v-else-if="data.item.value === 'team'"
                              >mdi-sitemap</v-icon
                            >
                            <v-icon color="primary" v-else>mdi-web</v-icon>
                          </v-avatar>
                          <span class="">{{ data.item.text }}</span>
                        </div>
                      </template>
                      <template v-slot:item="data">
                        <div class="">
                          <v-avatar left size="25">
                            <v-icon
                              color="primary"
                              v-if="data.item.value === 'individual'"
                              >mdi-account-circle-outline</v-icon
                            >
                            <v-icon
                              color="primary"
                              v-else-if="data.item.value === 'team'"
                              >mdi-sitemap</v-icon
                            >
                            <v-icon color="primary" v-else>mdi-web</v-icon>
                          </v-avatar>
                          <span class="">{{ data.item.text }}</span>
                        </div>
                      </template>
                    </v-select>
                  </v-col>
                  <v-col class="d-flex" cols="6" sm="6">
                    <v-autocomplete
                      :items="getFormattedTeams"
                      item-value="value"
                      item-text="text"
                      outlined
                      dense
                      label="Team"
                      multiple
                      append-outer-icon="mdi-information-variant"
                      v-model="keyResult.team"
                      :error-messages="keyResultErrors.team"
                      @click:append-outer="openInputInfo('team')"
                      :id="`input-info-${
                        getInpuInfoType === `team` ? getInpuInfoType : ''
                      }`"
                      v-if="keyResult.type === `team`"
                      disabled
                    >
                      <template v-slot:selection="data">
                        <div class="">
                          <v-avatar left size="25">
                            <v-icon color="primary">mdi-sitemap</v-icon>
                          </v-avatar>
                          <span class="">{{ `Team: ${data.item.text}` }}</span>
                        </div>
                      </template>
                      <template v-slot:item="data">
                        <div class="">
                          <v-avatar left size="25">
                            <v-icon color="primary">mdi-sitemap</v-icon>
                          </v-avatar>
                          <span class="">{{ `Team: ${data.item.text}` }}</span>
                        </div>
                      </template>
                    </v-autocomplete>
                  </v-col>
                </v-row>
                <v-row align="center">
                  <v-col class="d-flex" cols="6" sm="6">
                    <v-select
                      :items="objectivePeriods"
                      item-text="text"
                      item-value="value"
                      label="When"
                      dense
                      outlined
                      append-outer-icon="mdi-information-variant"
                      :error-messages="keyResultErrors.period.when"
                      v-model="keyResult.period.when"
                      @click:append-outer="openInputInfo('when')"
                      :id="`input-info-${
                        getInpuInfoType === `when` ? getInpuInfoType : ''
                      }`"
                    ></v-select>
                  </v-col>
                </v-row>
                <v-row align="center">
                  <v-col class="d-flex" cols="6" sm="6">
                    <v-menu
                      ref="startDate"
                      v-model="startDateMenu1"
                      :close-on-content-click="false"
                      transition="scale-transition"
                      offset-y
                      max-width="290px"
                      min-width="auto"
                      append-outer-icon="mdi-information-variant"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="startDateFormatted"
                          label="Start date"
                          v-bind="attrs"
                          v-on="on"
                          dense
                          outlined
                          append-outer-icon="mdi-information-variant"
                          :error-messages="keyResultErrors.period.startDate"
                          @click:append-outer="openInputInfo('startDate')"
                          :id="`input-info-${
                            getInpuInfoType === `startDate`
                              ? getInpuInfoType
                              : ''
                          }`"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="keyResult.period.startDate"
                        no-title
                        @input="startDateMenu1 = false"
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col class="d-flex" cols="6" sm="6">
                    <v-menu
                      ref="endDate"
                      v-model="endDateMenu1"
                      :close-on-content-click="false"
                      transition="scale-transition"
                      offset-y
                      max-width="290px"
                      min-width="auto"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="endDateFormatted"
                          label="End date"
                          v-bind="attrs"
                          v-on="on"
                          dense
                          outlined
                          append-outer-icon="mdi-information-variant"
                          @click:append-outer="openInputInfo('endState')"
                          :error-messages="keyResultErrors.period.endDate"
                          :id="`input-info-${
                            getInpuInfoType === `endState`
                              ? getInpuInfoType
                              : ''
                          }`"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="keyResult.period.endDate"
                        no-title
                        @input="endDateMenu1 = false"
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                </v-row>
                <s-accordions>
                  <s-accordion
                    label="More options"
                    labelRight="No tags, No description"
                    :sub="true"
                  >
                    <v-row align="center">
                      <v-col class="d-flex" cols="6" sm="6">
                        <v-text-field
                          label="Tags"
                          outlined
                          dense
                          placeholder="eg. tag1, tag2, tag3"
                          v-model="keyResult.tag"
                          :error-messages="objectiveErrors.tag"
                        ></v-text-field>
                      </v-col>
                      <v-col class="d-flex" cols="6" sm="6">
                        <v-text-field
                          label="Description"
                          placeholder="Objective description"
                          outlined
                          dense
                          v-model="keyResult.description"
                          :error-messages="keyResultErrors.description"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </s-accordion>
                </s-accordions>
              </s-accordion>
              <v-divider class="py-2"></v-divider>
              <s-accordion label="Alignment" labelRight="Get to 100% complete">
                <v-btn text color="primary"
                  ><v-icon>mdi-arrow-up-left</v-icon> Align objective</v-btn
                >
              </s-accordion>
            </s-accordions>

            <div class="create-obj-btns my-6">
              <v-btn
                text
                elevation="5"
                large
                class="mr-3"
                :disabled="isSubmitting"
                @click="cancelKeyResult()"
                >Cancel</v-btn
              >
              <v-btn
                type="submit"
                elevation="5"
                large
                color="primary"
                :loading="isSubmitting"
                >{{ isEditingKeyresult ? "Save" : "Add" }}</v-btn
              >
            </div>
          </form>
        </main>
      </section>
    </s-drawer>

    <dropper
      :join="`#input-info-${getInpuInfoType}`"
      ref="inputInfo"
      @esc-keydown="closeInputInfo"
      @other-area-clicked="closeInputInfo"
      class="dropper"
      :z-index="9999"
    >
      <!-- <div class="input-info">Input details</div> -->
      <!---->
    </dropper>
    <dropper
      :join="`#check-in-${getCheckInId}`"
      ref="checkIn"
      @esc-keydown="closeCheckIn"
      class="dropper"
      :z-index="2"
    >
      <div
        class="dropper__app v-application v-application--is-ltr theme--light"
      >
        <div class="content">
          <div class="content__header">
            <h3>New check in</h3>
            <span @click="cancelCheckIn()"><v-icon>mdi-close</v-icon></span>
          </div>
          <div class="content__body">
            <div class="kr__check__in__form">
              <h3>Edit progress and status</h3>
              <form @submit.prevent="saveCheckIn()">
                <v-select
                  :items="[
                    { value: 'ns', text: 'Not Started' },
                    { value: 'ar', text: 'At Risk' },
                    { value: 'bd', text: 'Behind' },
                    { value: 'ot', text: 'On Track' },
                    { value: 'cm', text: 'Completed' },
                  ]"
                  item-value="value"
                  item-text="text"
                  label="Status"
                  outlined
                  dense
                  readonly
                  v-model="krProgress.status"
                >
                  <template v-slot:selection="data">
                    <div class="progress-menu">
                      <div :class="`progress-status ${data.item.value}`"></div>
                      <span class="">{{ data.item.text }}</span>
                    </div>
                  </template>
                  <template v-slot:item="data">
                    <div class="progress-menu">
                      <div :class="`progress-status ${data.item.value}`"></div>
                      <span class="">{{ data.item.text }}</span>
                    </div>
                  </template>
                </v-select>
                <div class="range__wrapper mb-4">
                  <input
                    type="range"
                    :min="krProgress.start"
                    :max="krProgress.target"
                    v-model.number="krProgress.progress"
                  />
                  <span class="">{{ krProgress.progress }}</span>
                </div>
                <div id="target">( Target: {{ krProgress.target }})</div>
                <div class="create-obj-btns mt-4 mb-2">
                  <v-btn
                    text
                    elevation="5"
                    class="mr-3"
                    :disabled="isSubmitting"
                    @click="cancelCheckIn()"
                    >Cancel</v-btn
                  >
                  <v-btn
                    type="submit"
                    elevation="5"
                    color="primary"
                    :loading="isSubmitting"
                    >Check in</v-btn
                  >
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </dropper>
    <s-snackbar v-model="snackOpen">
      {{ snackMessage }}
    </s-snackbar>
  </section>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import SAccordions from "../accordion/SAccordions";
import SAccordion from "../accordion/SAccordion";
import SDrawer from "../SDrawer";
import SRange from "../SRange";
import SSnackbar from "../SSnackbar";
import { dateTime } from "../../mixins";

export default {
  name: "ObjectiveKeyresult",
  props: {
    // objectives: Array,
  },
  components: {
    SDrawer,
    SAccordions,
    SAccordion,
    SRange,
    SSnackbar,
  },
  mixins: [dateTime],
  computed: {
    ...mapGetters(["getUserObjectives", "getTeams"]),
    getInpuInfoType() {
      return this.inpuInfoType;
    },
    getCheckInId() {
      return this.checkInId;
    },
    getObjId() {
      return this.objId;
    },
    getFormattedTeams() {
      return this.getTeams.map((team) => {
        return {
          value: team.id,
          text: team.name,
        };
      });
    },
  },
  created() {
    this.objective.period.when = "month";
    this.keyResult.period.when = "month";
    this.$watch(
      () => this.objective.period.when,
      (val) => {
        this.setObjectiveDatePeriod(val);
      },
      { immediate: true }
    );
    this.$watch(
      () => this.keyResult.period.when,
      (val) => {
        this.setKeyresultDatePeriod(val);
      },
      { immediate: true }
    );
    this.$watch(
      () => this.objective.type,
      (val) => {
        if (val === "team") {
          this.showTeamInput = true;
        } else {
          this.showTeamInput = false;
          this.objective.team = "";
        }
      },
      { imediate: true }
    );
    this.$watch(
      () => this.objective.progress.type,
      (val) => {
        if (val === "manual") {
          this.progressOptions = [
            {
              value: "owner",
              text: "You are responsible for making check-ins",
            },
          ];
          this.objective.progress.option = "owner";
        } else {
          this.progressOptions = [
            { value: "percentage", text: "Percentage" },
            { value: "number", text: "Number" },
          ];
          this.objective.progress.option = "percentage";
        }
      },
      { imediate: true }
    );
    this.$watch(
      () => this.keyResult.progress.type,
      (val) => {
        if (val === "manual") {
          this.progressOptions1 = [
            {
              value: "owner",
              text: "You are responsible for making check-ins",
            },
          ];
          this.keyResult.progress.option = "owner";
        } else {
          this.progressOptions1 = [
            { value: "percentage", text: "Percentage" },
            { value: "number", text: "Number" },
          ];
          this.keyResult.progress.option = "percentage";
        }
      },
      { imediate: true }
    );
    this.$watch(
      () => this.keyResult.metric,
      (val) => {
        this.keyResult.title = `Increase ${this.keyResult.metric} from ${this.keyResult.starting} to ${this.keyResult.target}`;
      }
    );
    this.$watch(
      () => this.keyResult.target,
      (val) => {
        this.keyResult.title = `Increase ${this.keyResult.metric} from ${this.keyResult.starting} to ${val}`;
      }
    );
    this.$watch(
      () => this.keyResult.starting,
      (val) => {
        if (val) {
          this.keyResult.title = `Increase ${this.keyResult.metric} from ${val} to ${this.keyResult.target}`;
        } else {
          this.keyResult.starting = 1;
          this.keyResult.title = `Increase ${
            this.keyResult.metric
          } from ${1} to ${this.keyResult.target}`;
        }
      }
    );
    this.$watch(
      () => this.objective.period.startDate,
      (val) => {
        if (val) {
          this.startDateFormatted = this.formatDate2(val);
        }
      }
    );
    this.$watch(
      () => this.objective.period.endDate,
      (val) => {
        if (val) {
          this.endDateFormatted = this.formatDate2(val);
        }
      }
    );
    this.$watch(
      () => this.keyResult.period.startDate,
      (val) => {
        if (val) {
          this.startDateFormatted = this.formatDate2(val);
        }
      }
    );
    this.$watch(
      () => this.keyResult.period.endDate,
      (val) => {
        if (val) {
          this.endDateFormatted = this.formatDate2(val);
        }
      }
    );
    this.$watch(
      () => this.krProgress.progress,
      (val) => {
        const abjIndex = this.getUserObjectives.findIndex(
          (o) => o.id === this.getObjId
        );
        if (abjIndex !== -1) {
          const krIndex = this.getUserObjectives[
            abjIndex
          ].key_results.findIndex((o) => o.id === this.getCheckInId);
          const kr = this.getUserObjectives[abjIndex].key_results[krIndex];
          if (val === 0 || val <= kr.kr_metric.target) {
            this.krProgress.status = this.getStatus(
              val,
              kr.kr_metric.start,
              kr.kr_metric.target
            );
            this.dispatchObjective({
              type: "UPDATE_PROGRESS",
              payload: {
                objId: this.getObjId,
                krIndex,
                val,
              },
            });
          }
        }
      }
    );
  },
  data() {
    return {
      objectiveDrawer: false,
      keyResultDrawer: false,
      isSubmitting: false,
      isEditingKeyresult: false,
      isEditingOjective: false,
      showTeamInput: false,

      isSelectedTeam: false,
      isSelectedIndividual: false,
      isSelectedOrganization: false,
      isOjectiveAdded: false,

      inpuInfoType: "",
      checkInId: "",
      objId: "",
      krToEditId: "",
      ojectiveToEditId: "",

      startDate: null,
      endDate: null,
      startDateFormatted: this.formatDate2(new Date().toISOString()),
      endDateFormatted: this.formatDate2(new Date().toISOString()),
      startDateMenu: false,
      endDateMenu: false,
      startDateMenu1: false,
      endDateMenu1: false,
      closeKeyResult: false,

      when: "month",
      objectiveType: [
        { value: "individual", text: "Type: Individual" },
        { value: "team", text: "Type: Team" },
        { value: "organization", text: "Type: Organization" },
      ],
      progressType: [
        { value: "manual", text: "Manually" },
        { value: "automatic", text: "Automatic via rollup from key results" },
      ],
      progressOptions: [
        { value: "owner", text: "You are responsible for making check-ins" },
      ],
      progressType1: [
        { value: "manual", text: "Manually" },
        { value: "automatic", text: "Automatic via rollup from key results" },
      ],
      progressOptions1: [
        { value: "owner", text: "You are responsible for making check-ins" },
      ],

      objective: {
        title: "",
        tag: "",
        description: "",
        type: "",
        unit: "",
        team: [],
        period: {
          when: "",
          startDate: "",
          endDate: "",
        },
        progress: {
          type: "manual",
          option: "",
        },
      },
      keyResult: {
        objectiveId: "",
        metric: "",
        target: "",
        starting: 1,
        title: "",
        tag: "",
        description: "",
        type: "",
        team: "",
        period: {
          when: "",
          startDate: "",
          endDate: "",
        },
        progress: {
          type: "manual",
          option: "",
        },
      },
      objectiveErrors: {
        title: "",
        tag: "",
        description: "",
        type: "",
        unit: "",
        team: "",
        period: {
          when: "",
          startDate: "",
          endDate: "",
        },
        progress: {
          type: "",
          option: "",
        },
      },
      keyResultErrors: {
        metric: "",
        target: "",
        starting: "",
        title: "",
        tag: "",
        description: "",
        type: "",
        team: "",
        period: {
          when: "",
          startDate: "",
          endDate: "",
        },
        progress: {
          type: "",
          option: "",
        },
      },
      krProgress: {
        progress: "",
        status: "",
        start: "",
      },
    };
  },

  methods: {
    ...mapActions([
      "dispatchObjective",
      "dispatchTeam",
      "dispatchNotifications",
    ]),
    updateObjectiveProgress(e, objId) {
      this.getUserObjectives.forEach((obj) => {
        if (obj.id === objId) {
          this.krProgress.progress = parseInt(e.target.value);
          const krTotal = obj.key_results.reduce((total, progress) => {
            return (total += progress.kr_metric.progress);
          }, 0);
          const subTotal = ((krTotal / obj.metric.target) * 100).toFixed(2);
          if (subTotal.toString().includes(".00")) {
            obj.metric.progress = parseInt(subTotal.toString().split(".")[0]);
          } else {
            obj.metric.progress = parseFloat(subTotal);
          }
        }
      });
    },
    openObjectiveDrawer(type = null) {
      if (type) {
        this.objective.type = type.type;
        if (type.type === "team") {
          if (type.team !== "all") {
            this.isSelectedTeam = true;
            this.objective.team = [type.team];
          }
        } else if (type.type === "individual") {
          this.isSelectedIndividual = true;
        } else {
          this.isSelectedOrganization = true;
        }
      } else {
        this.isSelectedOrganization = false;
        this.isSelectedIndividual = false;
        this.isSelectedTeam = false;
      }
      this.isEditingOjective = false;
      this.objectiveDrawer = !this.objectiveDrawer;
      this.$nextTick(() => {
        if (!this.isEditingOjective) {
          this.objective.period.when = "month";
        }
      });
    },
    opOD() {
      this.$emit("openObjectiveDrawer");
    },
    openKeyResultDrawer(obj = null, close = null) {
      if (obj) {
        this.keyResult.objectiveId = obj.id;
        this.keyResult.type = obj.type;
        if (obj.team) {
          this.keyResult.team = [obj.team];
        }
      } else {
        this.keyResult.objectiveId = obj;
      }

      this.closeKeyResult = close;
      this.isEditingKeyresult = false;
      this.keyResultDrawer = !this.keyResultDrawer;
      if (!this.isEditingKeyresult) {
        this.keyResult.period.when = "month";
      }
    },
    openInputInfo(type) {
      this.inpuInfoType = type;
      const dropper = this.$refs.inputInfo;
      if (dropper) {
        dropper.open();
      }
    },
    closeInputInfo() {
      const dropper = this.$refs.inputInfo;
      if (dropper) {
        dropper.close();
        this.$nextTick(() => {
          this.inpuInfoType = "";
        });
      }
    },
    openCheckIn(objId, kr) {
      this.checkInId = kr.id;
      this.objId = objId;
      const dropper = this.$refs.checkIn;
      this.krProgress.progress = kr.kr_metric.progress;
      this.krProgress.target = kr.kr_metric.target;
      this.krProgress.start = kr.kr_metric.start;
      this.krProgress.status = this.getStatus(
        kr.kr_metric.progress,
        kr.kr_metric.start,
        kr.kr_metric.target
      );
      if (dropper) {
        dropper.open();
      }
    },
    closeCheckIn() {
      const dropper = this.$refs.checkIn;
      if (dropper) {
        dropper.close();
        this.$nextTick(() => {
          this.checkInId = "";
          this.objId = "";
        });
      }
    },
    addNewOjective() {
      this.isSubmitting = true;
      this.setObjectiveErrors();
      this.$axios
        .post(`/dashboard/add-new-objective`, {
          ...this.objective,
          period: {
            ...this.objective.period,
            startDate: new Date(this.objective.period.startDate).toISOString(),
            endDate: new Date(this.objective.period.endDate).toISOString(),
          },
        })
        .then((res) => {
          this.isSubmitting = false;
          if (res.status === 200 && res.data.created) {
            this.clearObjective();
            this.showSnackMessage("Objective successfully created.");
            this.dispatchObjective({
              type: "ADD_NEW_OBJECTIVE",
              payload: res.data.objective,
            });
            this.dispatchNotifications({
              type: "ADD_NOTIFICATION",
              payload: res.data.notification,
            });
            this.$nextTick(() => {
              this.isOjectiveAdded = true;
            });
          }
        })
        .catch((err) => {
          this.isSubmitting = false;
          this.isOjectiveAdded = false;
          if (err.response.status === 422) {
            if (err.response.data.insertionFailed) {
              this.showSnackMessage(err.response.data.insertionFailed);
              return;
            }
            this.setObjectiveErrors(err.response.data.errors);
          }
        });
    },
    updateOjective() {
      this.isSubmitting = true;
      this.setObjectiveErrors();
      this.$axios
        .put(`/dashboard/update-objective/${this.ojectiveToEditId}`, {
          ...this.objective,
          period: {
            ...this.objective.period,
            startDate: new Date(this.objective.period.startDate).toISOString(),
            endDate: new Date(this.objective.period.endDate).toISOString(),
          },
        })
        .then((res) => {
          this.isSubmitting = false;
          if (res.status === 200 && res.data.updated) {
            this.cancelObjective();
            this.showSnackMessage("Objective successfully updated.");
            this.dispatchObjective({
              type: "UPDATE_OBJECTIVE",
              payload: res.data.objective,
            });
          }
        })
        .catch((err) => {
          this.isSubmitting = false;
          if (err.response.status === 422) {
            if (err.response.data.updateFailed) {
              this.showSnackMessage(err.response.data.updateFailed);
              return;
            }
            this.setObjectiveErrors(err.response.data.errors);
          }
        });
    },
    deleteOjective(id) {
      this.$axios
        .delete(`/dashboard/delete-objective/${id}`)
        .then((res) => {
          if (res.status === 200 && res.data.deleted) {
            this.dispatchObjective({
              type: "DELETE_OBJECTIVE",
              payload: id,
            });
            this.showSnackMessage("Objective successfully deleted.");
          }
        })
        .catch((err) => {
          if (err.response.status === 422) {
            this.showSnackMessage(err.response.data.deletionFailed);
          } else {
            console.trace(err);
          }
        });
    },
    addNewKeyResult() {
      this.isSubmitting = true;
      this.setKeyResultErrors();
      this.$axios
        .post(`/dashboard/add-new-key-result`, {
          ...this.keyResult,
          period: {
            ...this.keyResult.period,
            startDate: new Date(this.keyResult.period.startDate).toISOString(),
            endDate: new Date(this.keyResult.period.endDate).toISOString(),
          },
        })
        .then((res) => {
          this.isSubmitting = false;
          if (res.status === 200 && res.data.created) {
            if (this.closeKeyResult) {
              this.cancelKeyResult();
            } else {
              this.clearKeyResult(res.data.keyResult.objective_id);
            }
            this.showSnackMessage("Key result successfully added.");
            this.dispatchObjective({
              type: "ADD_NEW_KEY_RESULT",
              payload: res.data.keyResult,
            });
            this.dispatchNotifications({
              type: "ADD_NOTIFICATION",
              payload: res.data.notification,
            });
          }
        })
        .catch((err) => {
          this.isSubmitting = false;
          if (err.response.status === 422) {
            if (err.response.data.insertionFailed) {
              this.showSnackMessage(err.response.data.insertionFailed);
              return;
            }
            this.setKeyResultErrors(err.response.data.errors);
          } else {
            console.trace(err);
          }
        });
    },
    updateKeyResult() {
      this.isSubmitting = true;
      this.setKeyResultErrors();
      this.$axios
        .put(`/dashboard/update-key-result/${this.krToEditId}`, {
          ...this.keyResult,
          period: {
            ...this.keyResult.period,
            startDate: new Date(this.keyResult.period.startDate).toISOString(),
            endDate: new Date(this.keyResult.period.endDate).toISOString(),
          },
        })
        .then((res) => {
          this.isSubmitting = false;
          if (res.status === 200 && res.data.updated) {
            this.cancelKeyResult();
            this.showSnackMessage("Key result successfully updated.");
            this.dispatchObjective({
              type: "UPDATE_KEY_RESULT",
              payload: res.data.keyResult,
            });
          }
        })
        .catch((err) => {
          this.isSubmitting = false;
          if (err.response.status === 422) {
            if (err.response.data.updateFailed) {
              this.showSnackMessage(err.response.data.updateFailed);
              return;
            }
            this.setKeyResultErrors(err.response.data.errors);
          } else {
            console.trace(err);
          }
        });
    },
    deleteKeyResult(objId, id) {
      this.$axios
        .delete(`/dashboard/delete-key-result/${id}`)
        .then((res) => {
          if (res.status === 200 && res.data.deleted) {
            this.dispatchObjective({
              type: "DELETE_KEY_RESULT",
              payload: { objId, id },
            });
            this.showSnackMessage("Key result successfully deleted.");
          }
        })
        .catch((err) => {
          if (err.response.status === 422) {
            this.showSnackMessage(err.response.data.deletionFailed);
          } else {
            console.trace(err);
          }
        });
    },
    setKeyResultToUpdate(obj, kr) {
      this.krToEditId = kr.id;
      this.keyResult = {
        objectiveId: kr.objective_id,
        metric: kr.metric,
        prevTarget: kr.kr_metric.target,
        target: kr.kr_metric.target,
        starting: kr.kr_metric.start,
        title: kr.title,
        tag: kr.tag,
        description: kr.description,
        period: {
          when: kr.period.when,
          startDate: kr.period.startDate.substr(0, 10),
          endDate: kr.period.endDate.substr(0, 10),
        },
        progress: {
          type: kr.progress.type,
          option: kr.progress.option,
        },
      };
      this.startDateFormatted = this.formatDate2(kr.period.startDate);
      this.endDateFormatted = this.formatDate2(kr.period.endDate);
      this.openKeyResultDrawer({
        id: kr.objective_id,
        type: obj.type,
        team: obj.pivot ? [obj.pivot.team_id] : [],
      });
      this.$nextTick(() => {
        this.isEditingKeyresult = true;
      });
    },
    setOjectiveToUpdate(obj) {
      this.ojectiveToEditId = obj.id;
      this.objective = {
        title: obj.title,
        tag: obj.tag,
        description: obj.description,
        type: obj.type,
        unit: obj.metric.unit,
        team: obj.pivot ? [obj.pivot.team_id] : [],
        period: {
          when: obj.period.when,
          startDate: obj.period.startDate.substr(0, 10),
          endDate: obj.period.endDate.substr(0, 10),
        },
        progress: {
          type: obj.progress.type,
          option: obj.progress.option,
        },
      };
      this.startDateFormatted = this.formatDate2(obj.period.startDate);
      this.endDateFormatted = this.formatDate2(obj.period.endDate);
      this.openObjectiveDrawer();
      this.isEditingOjective = true;
    },
    saveCheckIn() {
      this.isSubmitting = true;
      let data = null;
      const obj = this.getUserObjectives.find((o) => o.id === this.getObjId);
      if (obj) {
        const kr = obj.key_results.find((o) => o.id === this.getCheckInId);
        data = {
          objProgress: obj.metric.progress,
          krProgress: kr.kr_metric.progress,
          status: this.krProgress.status,
        };
      }
      if (data) {
        this.$axios
          .put(
            `/dashboard/save-check-in/${this.getObjId}/${this.getCheckInId}`,
            data
          )
          .then((res) => {
            this.isSubmitting = false;
            if (res.status === 200 && res.data.updated) {
              this.showSnackMessage("Check in save successfully.");
              this.dispatchObjective({
                type: "UPDATE_KEY_RESULT",
                payload: res.data.keyResult,
              });
              this.closeCheckIn();
            }
          })
          .catch((err) => {
            this.isSubmitting = false;
            console.trace(err);
          });
      }
    },
    setObjectiveErrors(err = {}) {
      this.objectiveErrors = {
        title: err.title || "",
        tag: err.tag || "",
        description: err.description || "",
        type: err.type || "",
        unit: err.unit || "",
        team: err.team || "",
        period: {
          when: err["period.when"] || "",
          startDate: err["period.startDate"] || "",
          endDate: err["period.endDate"] || "",
        },
        progress: {
          type: err["progress.type"] || "",
          option: err["progress.option"] || "",
        },
      };
    },
    setKeyResultErrors(err = {}) {
      this.keyResultErrors = {
        metric: err.metric || "",
        target: err.target || "",
        starting: err.starting || "",
        title: err.title || "",
        tag: err.tag || "",
        description: err.description || "",
        type: err.type || "",
        team: err.team || "",
        period: {
          when: err["period.when"] || "",
          startDate: err["period.startDate"] || "",
          endDate: err["period.endDate"] || "",
        },
        progress: {
          type: err["progress.type"] || "",
          option: err["progress.option"] || "",
        },
      };
    },
    clearObjective() {
      this.objective = {
        title: "",
        tag: "",
        description: "",
        type: "",
        unit: "",
        team: [],
        period: {
          when: "",
          startDate: "",
          endDate: "",
        },
        progress: {
          type: "",
          option: "",
        },
      };
    },
    clearKeyResult(objId = "") {
      this.keyResult = {
        objectiveId: objId,
        metric: "",
        target: "",
        starting: "",
        title: "",
        tag: "",
        description: "",
        type: [],
        team: "",
        period: {
          when: "",
          startDate: "",
          endDate: "",
        },
        progress: {
          type: "",
          option: "",
        },
      };
    },
    cancelObjective() {
      this.openObjectiveDrawer();
      this.$nextTick(() => {
        this.clearObjective();
        this.objective.period.when = "month";
      });
    },
    cancelKeyResult() {
      this.openKeyResultDrawer();
      this.$nextTick(() => {
        this.clearKeyResult();
        this.keyResult.period.when = "month";
        this.closeKeyResult = null;
      });
    },
    cancelCheckIn() {
      this.closeCheckIn();
    },

    setObjectiveDatePeriod(period) {
      switch (period) {
        case "Q1":
          this.startDateFormatted = this.formatDate2(this.getDate(1, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(3, 31));
          this.objective.period.startDate = this.getDate(1, 1).substr(0, 10);
          this.objective.period.endDate = this.getDate(3, 31).substr(0, 10);
          break;
        case "Q2":
          this.startDateFormatted = this.formatDate2(this.getDate(4, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(6, 30));
          this.objective.period.startDate = this.getDate(4, 1).substr(0, 10);
          this.objective.period.endDate = this.getDate(6, 30).substr(0, 10);
          break;
        case "Q3":
          this.startDateFormatted = this.formatDate2(this.getDate(7, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(9, 30));
          this.objective.period.startDate = this.getDate(7, 1).substr(0, 10);
          this.objective.period.endDate = this.getDate(9, 30).substr(0, 10);
          break;
        case "Q4":
          this.startDateFormatted = this.formatDate2(this.getDate(10, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(12, 31));
          this.objective.period.startDate = this.getDate(10, 1).substr(0, 10);
          this.objective.period.endDate = this.getDate(12, 31).substr(0, 10);
          break;
        case "firstHalf":
          this.startDateFormatted = this.formatDate2(this.getDate(1, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(6, 30));
          this.objective.period.startDate = this.getDate(1, 1).substr(0, 10);
          this.objective.period.endDate = this.getDate(6, 30).substr(0, 10);
          break;
        case "secondHalf":
          this.startDateFormatted = this.formatDate2(this.getDate(7, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(12, 31));
          this.objective.period.startDate = this.getDate(7, 1).substr(0, 10);
          this.objective.period.endDate = this.getDate(12, 31).substr(0, 10);
          break;
        case "annual":
          this.startDateFormatted = this.formatDate2(this.getDate(1, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(12, 31));
          this.objective.period.startDate = this.getDate(1, 1).substr(0, 10);
          this.objective.period.endDate = this.getDate(12, 31).substr(0, 10);
          break;
        default:
          const start = this.getDate(
            this.currentDate.getUTCMonth(),
            this.currentDate.getUTCDate(),
            this.currentDate.getUTCFullYear()
          );
          const end = this.getDate(
            this.currentDate.getUTCMonth() + 1,
            0,
            this.currentDate.getUTCFullYear()
          );
          this.startDateFormatted = this.formatDate2(start);
          this.endDateFormatted = this.formatDate2(end);
          this.objective.period.startDate = start.substr(0, 10);
          this.objective.period.endDate = end.substr(0, 10);
          break;
      }
    },
    setKeyresultDatePeriod(period) {
      switch (period) {
        case "Q1":
          this.startDateFormatted = this.formatDate2(this.getDate(1, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(3, 31));
          this.keyResult.period.startDate = this.getDate(1, 1).substr(0, 10);
          this.keyResult.period.endDate = this.getDate(3, 31).substr(0, 10);
          break;
        case "Q2":
          this.startDateFormatted = this.formatDate2(this.getDate(4, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(6, 30));
          this.keyResult.period.startDate = this.getDate(4, 1).substr(0, 10);
          this.keyResult.period.endDate = this.getDate(6, 30).substr(0, 10);
          break;
        case "Q3":
          this.startDateFormatted = this.formatDate2(this.getDate(7, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(9, 30));
          this.keyResult.period.startDate = this.getDate(7, 1).substr(0, 10);
          this.keyResult.period.endDate = this.getDate(9, 30).substr(0, 10);
          break;
        case "Q4":
          this.startDateFormatted = this.formatDate2(this.getDate(10, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(12, 31));
          this.keyResult.period.startDate = this.getDate(10, 1).substr(0, 10);
          this.keyResult.period.endDate = this.getDate(12, 31).substr(0, 10);
          break;
        case "firstHalf":
          this.startDateFormatted = this.formatDate2(this.getDate(1, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(6, 30));
          this.keyResult.period.startDate = this.getDate(1, 1).substr(0, 10);
          this.keyResult.period.endDate = this.getDate(6, 30).substr(0, 10);
          break;
        case "secondHalf":
          this.startDateFormatted = this.formatDate2(this.getDate(7, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(12, 31));
          this.keyResult.period.startDate = this.getDate(7, 1).substr(0, 10);
          this.keyResult.period.endDate = this.getDate(12, 31).substr(0, 10);
          break;
        case "annual":
          this.startDateFormatted = this.formatDate2(this.getDate(1, 1));
          this.endDateFormatted = this.formatDate2(this.getDate(12, 31));
          this.keyResult.period.startDate = this.getDate(1, 1).substr(0, 10);
          this.keyResult.period.endDate = this.getDate(12, 31).substr(0, 10);
          break;
        default:
          if (!this.isEditingKeyresult) {
            const start = this.getDate(
              this.currentDate.getUTCMonth(),
              this.currentDate.getUTCDate(),
              this.currentDate.getUTCFullYear()
            );
            const end = this.getDate(
              this.currentDate.getUTCMonth() + 1,
              0,
              this.currentDate.getUTCFullYear()
            );
            this.startDateFormatted = this.formatDate2(start);
            this.endDateFormatted = this.formatDate2(end);
            this.keyResult.period.startDate = start.substr(0, 10);
            this.keyResult.period.endDate = end.substr(0, 10);
          }
          break;
      }
    },
    getStatus(value, start, target) {
      const $target = parseInt((value / target) * 100);
      if (value === start || value === 0) {
        return "ns";
      }
      if ($target === parseInt(((1.0 * target) / target) * 100)) {
        return "cm";
      }
      if ($target >= parseInt(((0.5 * target) / target) * 100)) {
        return "ot";
      }
      if ($target < parseInt(((0.25 * target) / target) * 100)) {
        return "ar";
      }
      if ($target >= parseInt(((0.25 * target) / target) * 100)) {
        return "bd";
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.input-info {
  padding: 2rem;
  background-color: #fff;
  border-radius: 10px;
  border: 0px solid rgb(255, 255, 255);
  box-shadow: rgb(43 72 130 / 30%) 0px 0px 20px 1px;
}
</style>
