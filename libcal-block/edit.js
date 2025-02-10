import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

// Demo options (normally, these are dynamically pulled from the API)
const locationOptions = [
  { value: '4604', label: 'Main' },
  { value: '4605', label: 'Addison' },
  { value: '4685', label: 'Brooklyn' },
];
const categoryOptions = [
  { value: '43198', label: 'Book Sale' },
  { value: '58168', label: 'School of Arts & Culture' },
  { value: '74734', label: 'School of CLE' },
];
const tagOptions = [
  { value: '3260', label: '0 to 3: Read to Me' },
  { value: '4925', label: 'African American History & Culture' },
  { value: '4905', label: 'Animals & Pets' },
];

const BlockEdit = ({ attributes, setAttributes }) => {
  const blockProps = useBlockProps();
  const {
    colorScheme,
    numberOfEvents,
    eventDaysAhead,
    eventLocations,
    eventCategories,
    eventTags,
    showDescription,
  } = attributes;

  // Convert numbers to strings for SelectControl values.
  const selectedLocations = eventLocations.map(String);
  const selectedCategories = eventCategories.map(String);
  const selectedTags = eventTags.map(String);

  // Update attributes after converting selected values back to integers.
  const onChangeLocations = (selectedValues) => {
    setAttributes({ eventLocations: selectedValues.map((value) => parseInt(value, 10)) });
  };

  const onChangeCategories = (selectedValues) => {
    setAttributes({ eventCategories: selectedValues.map((value) => parseInt(value, 10)) });
  };

  const onChangeTags = (selectedValues) => {
    setAttributes({ eventTags: selectedValues.map((value) => parseInt(value, 10)) });
  };

  return (
    <div {...blockProps}>
      <InspectorControls>
        <PanelBody title={__('Color Settings', 'cpl')}>
          <SelectControl
            label={__('Color Scheme', 'cpl')}
            help={__('Select the color scheme for the events.', 'cpl')}
            value={colorScheme}
            options={[
              { value: 'dark', label: 'Dark' },
              { value: 'light', label: 'Light' },
            ]}
            onChange={(value) => setAttributes({ colorScheme: value })}
          />
        </PanelBody>

        <PanelBody title={__('Event Settings', 'cpl')}>
          <RangeControl
            label={__('Number of Events', 'cpl')}
            help={__('Set the number of events to display.', 'cpl')}
            value={numberOfEvents}
            onChange={(value) => setAttributes({ numberOfEvents: value })}
            min={1}
            max={8}
          />

          <RangeControl
            label={__('Days in Advance', 'cpl')}
            help={__('How many days ahead to fetch events.', 'cpl')}
            value={eventDaysAhead}
            onChange={(value) => setAttributes({ eventDaysAhead: value })}
            min={1}
            max={365}
          />

          <ToggleControl
            label={__('Show Event Descriptions', 'cpl')}
            help={__('Toggle display of event descriptions.', 'cpl')}
            checked={showDescription}
            onChange={(value) => setAttributes({ showDescription: value })}
          />

          <SelectControl
            label={__('Event Locations', 'cpl')}
            help={__('Select event locations (hold Ctrl/Cmd to select multiple).', 'cpl')}
            value={selectedLocations}
            options={locationOptions}
            onChange={onChangeLocations}
            multiple
          />

          <SelectControl
            label={__('Event Categories', 'cpl')}
            help={__('Select event categories (hold Ctrl/Cmd to select multiple).', 'cpl')}
            value={selectedCategories}
            options={categoryOptions}
            onChange={onChangeCategories}
            multiple
          />

          <SelectControl
            label={__('Event Tags', 'cpl')}
            help={__('Select event tags (hold Ctrl/Cmd to select multiple).', 'cpl')}
            value={selectedTags}
            options={tagOptions}
            onChange={onChangeTags}
            multiple
          />
        </PanelBody>
      </InspectorControls>

      {/* Renders a live preview via server-side rendering */}
      <ServerSideRender block="cpl/libcal-compact" attributes={attributes} />
    </div>
  );
};

export default BlockEdit;
