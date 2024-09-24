/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/blocks/index.tsx");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/blocks/index.scss":
/*!*******************************!*\
  !*** ./src/blocks/index.scss ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./src/blocks/index.scss?");

/***/ }),

/***/ "./src/blocks/index.tsx":
/*!******************************!*\
  !*** ./src/blocks/index.tsx ***!
  \******************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/url */ \"@wordpress/url\");\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/api-fetch */ \"@wordpress/api-fetch\");\n/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils */ \"./src/blocks/utils.tsx\");\n/* harmony import */ var _shortcodes__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./shortcodes */ \"./src/blocks/shortcodes.tsx\");\n\n\n\n\n\n\nObject(_shortcodes__WEBPACK_IMPORTED_MODULE_5__[\"registerShortcodeBlock\"])({\n    name: 'all-books',\n    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('All Books', 'mybooktable'),\n    icon: 'book-alt',\n    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('List all your books in an embedded book listing.', 'mybooktable'),\n    inspectorAttributes: [\n        {\n            name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Display Settings', 'mybooktable'),\n            attributes: [\n                {\n                    name: 'gridview',\n                    type: 'checkbox',\n                    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Force Grid View Display?', 'mybooktable'),\n                    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Shows book covers in a responsive grid. (requires Professional or Developer Upgrade)', 'mybooktable'),\n                    default: false,\n                },\n                {\n                    name: 'show_header',\n                    type: 'checkbox',\n                    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Show Header?', 'mybooktable'),\n                    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Shows the taxonomy title and description above the book listing', 'mybooktable'),\n                    default: true,\n                },\n                {\n                    name: 'max_books',\n                    type: 'number',\n                    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Max Books to Display', 'mybooktable'),\n                    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Sets the maximum number of books that will be shown (Leave blank for no maximum)', 'mybooktable'),\n                    default: '',\n                },\n            ],\n        },\n    ],\n    toShortcode: attributes => {\n        const header = attributes.show_header ? '' : ' header=\"hidden\"';\n        const gridview = attributes.gridview ? ' gridview=\"true\"' : '';\n        const maxBooks = attributes.max_books ? ` max_books=\"${attributes.max_books}\"` : '';\n        return `[mybooktable${header}${gridview}${maxBooks}]`;\n    },\n});\nfunction registerTaxonomyShortcodeBlock(options) {\n    Object(_shortcodes__WEBPACK_IMPORTED_MODULE_5__[\"registerShortcodeBlock\"])({\n        name: `book-${options.taxonomy}`,\n        title: options.title,\n        icon: options.icon,\n        description: options.description,\n        data: _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default()({ path: Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_2__[\"addQueryArgs\"])(`/wp/v2/mbt_${options.taxonomy}`, { per_page: -1 }) }),\n        preview: (attributes, data, edit) => {\n            const term = data ? lodash__WEBPACK_IMPORTED_MODULE_0___default.a.find(data, { slug: attributes[options.taxonomy] }) : null;\n            return wp.element.createElement(\"div\", { className: \"attribute\" },\n                wp.element.createElement(\"span\", { className: \"name\" }, options.singular),\n                term ? wp.element.createElement(\"span\", { className: \"value\", onClick: edit }, term.name) : wp.element.createElement(\"span\", { className: \"no-value\" }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('None', 'mybooktable')));\n        },\n        attributes: [\n            {\n                name: options.taxonomy,\n                type: 'dropdown',\n                title: options.singular,\n                choices: (attributes, data) => data ? data.map(term => ({ label: term.name, value: term.slug })) : [],\n                default: '',\n            },\n        ],\n        inspectorAttributes: [\n            {\n                name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Display Settings', 'mybooktable'),\n                attributes: [\n                    {\n                        name: 'gridview',\n                        type: 'checkbox',\n                        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Force Grid View Display?', 'mybooktable'),\n                        description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Shows book covers in a responsive grid. (requires Professional or Developer Upgrade)', 'mybooktable'),\n                        default: false,\n                    },\n                    {\n                        name: 'show_header',\n                        type: 'checkbox',\n                        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Show Header?', 'mybooktable'),\n                        description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Shows the taxonomy title and description above the book listing', 'mybooktable'),\n                        default: true,\n                    },\n                    {\n                        name: 'max_books',\n                        type: 'number',\n                        title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Max Books to Display', 'mybooktable'),\n                        description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Sets the maximum number of books that will be shown (Leave blank for no maximum)', 'mybooktable'),\n                        default: '',\n                    },\n                ],\n            },\n        ],\n        toShortcode: attributes => {\n            if (!attributes[options.taxonomy]) {\n                return '';\n            }\n            const header = attributes.show_header ? '' : ' header=\"hidden\"';\n            const gridview = attributes.gridview ? ' gridview=\"true\"' : '';\n            const maxBooks = attributes.max_books ? ` max_books=\"${attributes.max_books}\"` : '';\n            return `[mybooktable ${options.taxonomy}=\"${attributes[options.taxonomy]}\"${header}${gridview}${maxBooks}]`;\n        },\n    });\n}\nregisterTaxonomyShortcodeBlock({\n    taxonomy: 'series',\n    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`All Books in Series`, 'mybooktable'),\n    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`List all the books in a given series in an embedded book listing.`, 'mybooktable'),\n    singular: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`Series`, 'mybooktable'),\n    icon: 'admin-page',\n});\nregisterTaxonomyShortcodeBlock({\n    taxonomy: 'genre',\n    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`All Books in Genre`, 'mybooktable'),\n    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`List all the books in a given genre in an embedded book listing.`, 'mybooktable'),\n    singular: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`Genre`, 'mybooktable'),\n    icon: 'category',\n});\nregisterTaxonomyShortcodeBlock({\n    taxonomy: 'tag',\n    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`All Books with Tag`, 'mybooktable'),\n    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`List all the books with a given tag in an embedded book listing.`, 'mybooktable'),\n    singular: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`Tag`, 'mybooktable'),\n    icon: 'tag',\n});\nregisterTaxonomyShortcodeBlock({\n    taxonomy: 'author',\n    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`All Books by Author`, 'mybooktable'),\n    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`List all the books written by a given author in an embedded book listing.`, 'mybooktable'),\n    singular: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])(`Author`, 'mybooktable'),\n    icon: 'admin-users',\n});\nObject(_shortcodes__WEBPACK_IMPORTED_MODULE_5__[\"registerShortcodeBlock\"])({\n    name: 'single-book',\n    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Single Book', 'mybooktable'),\n    icon: 'book-alt',\n    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Show a given book in an embedded book listing.', 'mybooktable'),\n    data: _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_3___default()({ path: Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_2__[\"addQueryArgs\"])('/wp/v2/mbt_book', { per_page: -1 }) }),\n    preview: (attributes, data, edit) => {\n        const book = data ? lodash__WEBPACK_IMPORTED_MODULE_0___default.a.find(data, { slug: attributes.book }) : null;\n        return wp.element.createElement(\"div\", { className: \"attribute\" },\n            wp.element.createElement(\"span\", { className: \"name\" }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Book', 'mybooktable')),\n            book ? wp.element.createElement(\"span\", { className: \"value\", onClick: edit }, book.title.rendered) : wp.element.createElement(\"span\", { className: \"no-value\" }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('None', 'mybooktable')));\n    },\n    attributes: [\n        {\n            name: 'book',\n            type: 'dropdown',\n            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Book', 'mybooktable'),\n            choices: (attributes, data) => data ? data.map(book => ({ label: book.title.rendered, value: book.slug })) : [],\n            default: '',\n        },\n    ],\n    inspectorAttributes: [\n        {\n            name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Display Settings', 'mybooktable'),\n            attributes: [\n                {\n                    name: 'display',\n                    type: 'dropdown',\n                    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Display Style', 'mybooktable'),\n                    choices: [\n                        { label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Default', 'mybooktable'), value: 'default' },\n                        { label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Summary', 'mybooktable'), value: 'summary' },\n                        { label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Buy Buttons', 'mybooktable'), value: 'buybuttons' },\n                        { label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Cover and Buy Buttons', 'mybooktable'), value: 'cover+buybuttons' },\n                    ],\n                    noEmptyOption: true,\n                    default: 'default',\n                },\n                {\n                    name: 'buybutton_shadowbox',\n                    type: 'checkbox',\n                    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Force Shadow Box for Buy Buttons?', 'mybooktable'),\n                    default: false,\n                },\n            ],\n        },\n    ],\n    toShortcode: attributes => {\n        if (!attributes.book) {\n            return '';\n        }\n        const display = attributes.display !== 'default' ? ` display=\"${attributes.display}\"` : '';\n        const buybuttonShadowbox = attributes.buybutton_shadowbox ? ' buybutton_shadowbox=\"true\"' : '';\n        return `[mybooktable book=\"${attributes.book}\"${display}${buybuttonShadowbox}]`;\n    },\n});\nObject(_shortcodes__WEBPACK_IMPORTED_MODULE_5__[\"registerShortcodeBlock\"])({\n    name: 'term-list',\n    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('All Terms in Taxonomy', 'mybooktable'),\n    icon: 'list-view',\n    description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('This allows you to display all of the different items in a MyBookTable taxonomy.', 'mybooktable'),\n    preview: (attributes, data, edit) => {\n        return wp.element.createElement(\"div\", { className: \"attribute\" },\n            wp.element.createElement(\"span\", { className: \"name\" }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Taxonomy', 'mybooktable')),\n            attributes.taxonomy ? wp.element.createElement(\"span\", { className: \"value\", onClick: edit }, Object(_utils__WEBPACK_IMPORTED_MODULE_4__[\"capitalize\"])(attributes.taxonomy)) : wp.element.createElement(\"span\", { className: \"no-value\" }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('None', 'mybooktable')));\n    },\n    attributes: [\n        {\n            name: 'taxonomy',\n            type: 'dropdown',\n            title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Taxonomy', 'mybooktable'),\n            choices: [\n                { label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Series', 'mybooktable'), value: 'series' },\n                { label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Genres', 'mybooktable'), value: 'genres' },\n                { label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Tags', 'mybooktable'), value: 'tags' },\n                { label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Authors', 'mybooktable'), value: 'authors' },\n            ],\n            default: '',\n        },\n    ],\n    inspectorAttributes: [\n        {\n            name: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Display Settings', 'mybooktable'),\n            attributes: [\n                {\n                    name: 'display',\n                    type: 'dropdown',\n                    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Display Style', 'mybooktable'),\n                    choices: [\n                        { label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Listing', 'mybooktable'), value: 'listing' },\n                        { label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Menu Bar', 'mybooktable'), value: 'bar' },\n                        { label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Simple', 'mybooktable'), value: 'simple' },\n                    ],\n                    noEmptyOption: true,\n                    default: 'listing',\n                },\n            ],\n        },\n    ],\n    toShortcode: attributes => {\n        if (!attributes.taxonomy) {\n            return '';\n        }\n        return `[mybooktable list=\"${attributes.taxonomy}\" display=\"${attributes.display}\"]`;\n    },\n});\n\n\n//# sourceURL=webpack:///./src/blocks/index.tsx?");

/***/ }),

/***/ "./src/blocks/shortcodes.tsx":
/*!***********************************!*\
  !*** ./src/blocks/shortcodes.tsx ***!
  \***********************************/
/*! exports provided: registerShortcodeBlock */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"registerShortcodeBlock\", function() { return registerShortcodeBlock; });\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/blocks */ \"@wordpress/blocks\");\n/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/editor */ \"@wordpress/editor\");\n/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/element */ \"@wordpress/element\");\n/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ \"@wordpress/components\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils */ \"./src/blocks/utils.tsx\");\n/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./index.scss */ \"./src/blocks/index.scss\");\n/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_index_scss__WEBPACK_IMPORTED_MODULE_7__);\n\n\n\n\n\n\n\n\nclass ShortcodeBlockEditor extends _wordpress_element__WEBPACK_IMPORTED_MODULE_4__[\"Component\"] {\n    constructor(props) {\n        super(props);\n        this.state = {\n            loading: false,\n            data: null,\n        };\n        this.toggleEdit = this.toggleEdit.bind(this);\n        this.attributeChange = lodash__WEBPACK_IMPORTED_MODULE_0___default.a.memoize(this.attributeChange.bind(this));\n        this.renderAttribute = this.renderAttribute.bind(this);\n    }\n    componentDidMount() {\n        if (this.props.options.data) {\n            this.setState({ loading: true });\n            this.props.options.data.then(data => this.setState({ data, loading: false }));\n        }\n    }\n    toggleEdit() {\n        this.props.setAttributes({ editMode: !this.props.attributes.editMode });\n    }\n    attributeChange(attribute) {\n        if (attribute.type === 'checkbox') {\n            return () => this.props.setAttributes({ [attribute.name]: !this.props.attributes[attribute.name] });\n        }\n        else if (attribute.type === 'number') {\n            return (value) => {\n                const numberValue = Math.floor(Number(value));\n                return this.props.setAttributes({ [attribute.name]: numberValue <= 0 ? '' : numberValue.toString() });\n            };\n        }\n        else {\n            return (value) => this.props.setAttributes({ [attribute.name]: value });\n        }\n    }\n    renderAttribute(attribute) {\n        const { attributes } = this.props;\n        const { data } = this.state;\n        if (attribute.type === 'dropdown') {\n            const choices = lodash__WEBPACK_IMPORTED_MODULE_0___default.a.isFunction(attribute.choices) ? attribute.choices(attributes, data) : attribute.choices;\n            return wp.element.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"SelectControl\"], { key: attribute.name, label: attribute.title, help: attribute.description, value: attributes[attribute.name], options: (attribute.noEmptyOption ? [] : [{ label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('-- Choose One --', 'mybooktable'), value: '' }]).concat(choices), onChange: this.attributeChange(attribute) });\n        }\n        else if (attribute.type === 'checkbox') {\n            return wp.element.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"ToggleControl\"], { key: attribute.name, label: attribute.title, help: attribute.description, checked: attributes[attribute.name], onChange: this.attributeChange(attribute) });\n        }\n        else if (attribute.type === 'radio') {\n            return wp.element.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"RadioControl\"], { key: attribute.name, label: attribute.title, help: attribute.description, value: attributes[attribute.name], options: attribute.choices, onChange: this.attributeChange(attribute) });\n        }\n        else if (attribute.type === 'textarea') {\n            return wp.element.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"TextareaControl\"], { key: attribute.name, label: attribute.title, help: attribute.description, value: attributes[attribute.name], onChange: this.attributeChange(attribute) });\n        }\n        else if (attribute.type === 'text') {\n            return wp.element.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"TextControl\"], { key: attribute.name, label: attribute.title, help: attribute.description, value: attributes[attribute.name], onChange: this.attributeChange(attribute) });\n        }\n        else if (attribute.type === 'number') {\n            return wp.element.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"TextControl\"], { key: attribute.name, label: attribute.title, help: attribute.description, type: \"number\", value: attributes[attribute.name], onChange: this.attributeChange(attribute) });\n        }\n        return Object(_utils__WEBPACK_IMPORTED_MODULE_6__[\"assertNever\"])(attribute);\n    }\n    render() {\n        const { attributes, options } = this.props;\n        const { loading, data } = this.state;\n        return wp.element.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__[\"Fragment\"], null,\n            options.attributes && wp.element.createElement(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__[\"BlockControls\"], null,\n                wp.element.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"Toolbar\"], { controls: [{ icon: 'edit', title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Edit'), onClick: this.toggleEdit, isActive: attributes.editMode }] })),\n            options.inspectorAttributes && wp.element.createElement(_wordpress_editor__WEBPACK_IMPORTED_MODULE_3__[\"InspectorControls\"], null, options.inspectorAttributes.map(section => wp.element.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"PanelBody\"], { title: section.name, key: section.name }, section.attributes.map(this.renderAttribute)))),\n            wp.element.createElement(\"div\", { className: `mbt-shortcode-block-editor ${options.name}` },\n                wp.element.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"Placeholder\"], null,\n                    wp.element.createElement(\"div\", { className: \"header\" },\n                        wp.element.createElement(\"div\", { className: \"logo\" }),\n                        wp.element.createElement(\"div\", null,\n                            wp.element.createElement(\"h2\", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('MyBookTable', 'mybooktable')),\n                            wp.element.createElement(\"h1\", null, options.title))),\n                    attributes.editMode && options.attributes && wp.element.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__[\"Fragment\"], null,\n                        wp.element.createElement(\"div\", { className: \"description\" }, options.description),\n                        wp.element.createElement(\"div\", { className: \"attributes\" }, loading ? wp.element.createElement(\"div\", { className: \"spinner-holder\" },\n                            wp.element.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"Spinner\"], null)) : options.attributes.map(this.renderAttribute)),\n                        wp.element.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__[\"Button\"], { isDefault: true, onClick: this.toggleEdit }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('Done', 'mybooktable'))),\n                    !attributes.editMode && options.preview && wp.element.createElement(\"div\", { className: \"preview\" }, options.preview(attributes, data, this.toggleEdit)))));\n    }\n}\nfunction getAttributeDataType(attribute) {\n    if (attribute.type === 'dropdown') {\n        return 'string';\n    }\n    else if (attribute.type === 'checkbox') {\n        return 'boolean';\n    }\n    else if (attribute.type === 'radio') {\n        return 'string';\n    }\n    else if (attribute.type === 'textarea') {\n        return 'string';\n    }\n    else if (attribute.type === 'text') {\n        return 'string';\n    }\n    else if (attribute.type === 'number') {\n        return 'string';\n    }\n    return Object(_utils__WEBPACK_IMPORTED_MODULE_6__[\"assertNever\"])(attribute);\n}\nfunction registerShortcodeBlock(options) {\n    const attributes = lodash__WEBPACK_IMPORTED_MODULE_0___default.a.flatten((options.inspectorAttributes || []).map(section => section.attributes)).concat(options.attributes || []);\n    Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_2__[\"registerBlockType\"])('mybooktable/' + options.name, {\n        title: options.title,\n        icon: options.icon,\n        category: 'mybooktable',\n        keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__[\"__\"])('MyBookTable', 'mybooktable')],\n        description: options.description,\n        attributes: lodash__WEBPACK_IMPORTED_MODULE_0___default.a.assign(lodash__WEBPACK_IMPORTED_MODULE_0___default.a.fromPairs(attributes.map(attribute => [attribute.name, { type: getAttributeDataType(attribute), default: attribute.default }])), { editMode: { type: 'boolean', default: true } }),\n        edit: (props) => wp.element.createElement(ShortcodeBlockEditor, { attributes: props.attributes, setAttributes: props.setAttributes, options: options }),\n        save: (props) => wp.element.createElement(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__[\"RawHTML\"], null, options.toShortcode(props.attributes)),\n    });\n}\n\n\n//# sourceURL=webpack:///./src/blocks/shortcodes.tsx?");

/***/ }),

/***/ "./src/blocks/utils.tsx":
/*!******************************!*\
  !*** ./src/blocks/utils.tsx ***!
  \******************************/
/*! exports provided: assertNever, capitalize */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"assertNever\", function() { return assertNever; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"capitalize\", function() { return capitalize; });\nfunction assertNever(value) {\n    throw new Error(`Unexpected value ${value}`);\n}\nfunction capitalize(str) {\n    return str.charAt(0).toUpperCase() + str.slice(1);\n}\n\n\n//# sourceURL=webpack:///./src/blocks/utils.tsx?");

/***/ }),

/***/ "@wordpress/api-fetch":
/*!******************************!*\
  !*** external "wp.apiFetch" ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = wp.apiFetch;\n\n//# sourceURL=webpack:///external_%22wp.apiFetch%22?");

/***/ }),

/***/ "@wordpress/blocks":
/*!****************************!*\
  !*** external "wp.blocks" ***!
  \****************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = wp.blocks;\n\n//# sourceURL=webpack:///external_%22wp.blocks%22?");

/***/ }),

/***/ "@wordpress/components":
/*!********************************!*\
  !*** external "wp.components" ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = wp.components;\n\n//# sourceURL=webpack:///external_%22wp.components%22?");

/***/ }),

/***/ "@wordpress/editor":
/*!****************************!*\
  !*** external "wp.editor" ***!
  \****************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = wp.editor;\n\n//# sourceURL=webpack:///external_%22wp.editor%22?");

/***/ }),

/***/ "@wordpress/element":
/*!*****************************!*\
  !*** external "wp.element" ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = wp.element;\n\n//# sourceURL=webpack:///external_%22wp.element%22?");

/***/ }),

/***/ "@wordpress/i18n":
/*!**************************!*\
  !*** external "wp.i18n" ***!
  \**************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = wp.i18n;\n\n//# sourceURL=webpack:///external_%22wp.i18n%22?");

/***/ }),

/***/ "@wordpress/url":
/*!*************************!*\
  !*** external "wp.url" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = wp.url;\n\n//# sourceURL=webpack:///external_%22wp.url%22?");

/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = lodash;\n\n//# sourceURL=webpack:///external_%22lodash%22?");

/***/ })

/******/ });