/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, Placeholder } from '@wordpress/components';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';
import '../assets/css/custom-reactions.css';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, isSelected, setAttributes }) {
  return (
    <div {...useBlockProps()}>
      <div class="custom-reactions">
        <span
          class="custom-reaction smile"
          data-reaction-type="smile"
          data-count="3"
        >
          <p>🙂</p>
          <span class="reaction-label">Smile</span>
        </span>
        <span
          class="custom-reaction straight"
          data-reaction-type="straight"
          data-count="0"
        >
          <p>😐</p>
          <span class="reaction-label">Straight</span>
        </span>
        <span
          class="custom-reaction sad"
          data-reaction-type="sad"
          data-count="0"
        >
          <p>🙁</p>
          <span class="reaction-label">Sad</span>
        </span>
      </div>
    </div>
  );
}
