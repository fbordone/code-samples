import { registerBlockType } from '@wordpress/blocks';
import block from './block.json';
import edit from './edit';

registerBlockType(block, {
  edit,
  save: () => null, // Output handled via server-side rendering in markup.php.
});
