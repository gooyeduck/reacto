/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';
import '../assets/css/custom-reactions.css';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save() {
  const blockProps = useBlockProps.save();
  return (
    <div {...blockProps}>
      <div class="custom-reactions">
        <span
          class="custom-reaction smile"
          data-reaction-type="smile"
          data-count="3"
        >
          <FontAwesomeIcon icon="fa-regular fa-face-smile" />
          <span class="reaction-label">Smile</span>
        </span>
        <span
          class="custom-reaction straight"
          data-reaction-type="straight"
          data-count="0"
        >
          <FontAwesomeIcon icon="fa-regular fa-face-meh" />
          <span class="reaction-label">Straight</span>
        </span>
        <span
          class="custom-reaction sad"
          data-reaction-type="sad"
          data-count="0"
        >
          <FontAwesomeIcon icon="fa-regular fa-face-frown" />
          <span class="reaction-label">Sad</span>
        </span>
      </div>
    </div>
  );
}
