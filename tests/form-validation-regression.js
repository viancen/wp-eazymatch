const fs = require('fs');
const path = require('path');
const vm = require('vm');

const code = fs.readFileSync(path.join(__dirname, '..', 'assets/scripts/emol.js'), 'utf8');
const context = {
	console: console,
	window: {},
};
context.window = context;
vm.runInNewContext(code, context);

const form = context.EazyWP.form;

function assert(condition, message) {
	if (!condition) {
		throw new Error(message);
	}
}

assert(form.isValidEmail('naam@bedrijf.nl') === true, 'A normal e-mail must pass.');
assert(form.isValidEmail('invalid') === false, 'A value without @ must fail.');
assert(form.isValidEmail('a@b') === false, 'A value without a dot in the domain must fail.');
assert(form.isValidEmail('') === false, 'An empty e-mail must fail.');

assert(form.isEmailField({ id: 'emol-email', type: 'text', className: '' }) === true, 'The apply e-mail field must be detected by id.');
assert(form.isEmailField({ id: 'x', type: 'email', className: '' }) === true, 'type=email must be detected.');
assert(form.isEmailField({ id: 'x', type: 'text', className: 'emol-text-input email required' }) === true, 'The email class must be detected.');
assert(form.isEmailField({ id: 'emol-firstname', type: 'text', className: 'required' }) === false, 'A name field is not an e-mail field.');

assert(form.isFilled({ type: 'text', value: 'Ada' }) === true, 'A filled text field must pass.');
assert(form.isFilled({ type: 'text', value: '   ' }) === false, 'Whitespace-only values must fail.');
assert(form.isFilled({ type: 'checkbox', checked: true }) === true, 'A checked box must pass.');
assert(form.isFilled({ type: 'checkbox', checked: false }) === false, 'An unchecked box must fail.');
assert(form.isFilled({ type: 'file', files: { length: 1 } }) === true, 'A chosen file must pass.');
assert(form.isFilled({ type: 'file', files: { length: 0 } }) === false, 'An empty file field must fail.');

context.EmolForm = { required: 'verplicht veld', email: 'gebruik een geldig mailadres', wait: 'Een moment geduld' };
const messages = form.messages();
assert(messages.required === 'verplicht veld', 'Localized required text must be used.');
assert(messages.email === 'gebruik een geldig mailadres', 'Localized e-mail text must be used.');
assert(messages.wait === 'Een moment geduld', 'Localized wait text must be used.');

console.log('Form validation regression checks passed.');
