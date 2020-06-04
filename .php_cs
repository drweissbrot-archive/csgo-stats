<?php

return PhpCsFixer\Config::create()
	->setRiskyAllowed(true)
	->setIndent("\t")
	->setRules([
		'align_multiline_comment' => ['comment_type' => 'all_multiline'],
		'array_indentation' => true,
		'array_syntax' => ['syntax' => 'short'],
		'backtick_to_shell_exec' => true,
		'binary_operator_spaces' => true,
		'blank_line_after_namespace' => true,
		'blank_line_after_opening_tag' => true,
		'blank_line_before_statement' => [
			'statements' => [
				'continue', 'declare', 'return', 'throw', 'try', 'if', 'goto',
			],
		],
		'braces' => true,
		'cast_spaces' => true,
		'class_attributes_separation' => true,
		'class_definition' => true,
		'combine_consecutive_issets' => true,
		'combine_consecutive_unsets' => true,
		'concat_space' => ['spacing' => 'one'],
		'declare_equal_normalize' => ['space' => 'single'],
		'dir_constant' => true,
		'doctrine_annotation_array_assignment' => true,
		'doctrine_annotation_braces' => true,
		'doctrine_annotation_indentation' => true,
		'doctrine_annotation_spaces' => true,
		'elseif' => true,
		'encoding' => true,
		'ereg_to_preg' => true,
		'escape_implicit_backslashes' => true,
		'explicit_indirect_variable' => true,
		'explicit_string_variable' => true,
		'full_opening_tag' => true,
		'fully_qualified_strict_types' => true,
		'function_declaration' => true,
		'function_to_constant' => true,
		'function_typehint_space' => true,
		'heredoc_to_nowdoc' => true,
		'include' => true,
		'indentation_type' => true,
		'is_null' => true,
		'line_ending' => true,
		'linebreak_after_opening_tag' => true,
		'list_syntax' => ['syntax' => 'short'],
		'logical_operators' => true,
		'lowercase_cast' => true,
		'lowercase_constants' => true,
		'lowercase_keywords' => true,
		'lowercase_static_reference' => true,
		'magic_constant_casing' => true,
		'mb_str_functions' => true,
		'method_argument_space' => true,
		'method_chaining_indentation' => true,
		'method_separation' => true,
		'modernize_types_casting' => true,
		'multiline_comment_opening_closing' => true,
		'multiline_whitespace_before_semicolons' => true,
		'native_constant_invocation' => true,
		'native_function_casing' => true,
		'native_function_invocation' => false,
		'no_alias_functions' => true,
		'no_binary_string' => true,
		'no_blank_lines_after_class_opening' => true,
		'no_blank_lines_after_phpdoc' => true,
		'no_break_comment' => true,
		'no_closing_tag' => true,
		'no_empty_phpdoc' => true,
		'no_empty_statement' => true,
		'no_extra_blank_lines' => true,
		'no_homoglyph_names' => true,
		'no_leading_import_slash' => true,
		'no_leading_namespace_whitespace' => true,
		'no_mixed_echo_print' => true,
		'no_multiline_whitespace_around_double_arrow' => true,
		'no_null_property_initialization' => true,
		'no_php4_constructor' => true,
		'no_short_bool_cast' => true,
		'no_short_echo_tag' => true,
		'no_singleline_whitespace_before_semicolons' => true,
		'no_spaces_after_function_name' => true,
		'no_spaces_around_offset' => true,
		'no_spaces_inside_parenthesis' => true,
		'no_superfluous_elseif' => true,
		'no_superfluous_phpdoc_tags' => true,
		'no_trailing_comma_in_list_call' => true,
		'no_trailing_comma_in_singleline_array' => true,
		'no_trailing_whitespace' => true,
		'no_trailing_whitespace_in_comment' => true,
		'no_unneeded_control_parentheses' => true,
		'no_unneeded_curly_braces' => true,
		'no_unneeded_final_method' => true,
		'no_unreachable_default_argument_value' => true,
		'no_unused_imports' => true,
		'no_useless_else' => true,
		'no_useless_return' => true,
		'no_whitespace_before_comma_in_array' => true,
		'no_whitespace_in_blank_line' => true,
		'normalize_index_brace' => true,
		'not_operator_with_successor_space' => true,
		'object_operator_without_whitespace' => true,
		'ordered_class_elements' => true,
		'ordered_imports' => true,
		'php_unit_construct' => true,
		'php_unit_dedicate_assert' => true,
		'php_unit_expectation' => true,
		'php_unit_fqcn_annotation' => true,
		'php_unit_namespaced' => true,
		'php_unit_no_expectation_annotation' => true,
		'php_unit_test_annotation' => true,
		'phpdoc_add_missing_param_annotation' => ['only_untyped' => true],
		'phpdoc_align' => true,
		'phpdoc_annotation_without_dot' => true,
		'phpdoc_indent' => true,
		'phpdoc_inline_tag' => true,
		'phpdoc_no_access' => true,
		'phpdoc_no_alias_tag' => true,
		'phpdoc_no_empty_return' => true,
		'phpdoc_no_package' => true,
		'phpdoc_no_useless_inheritdoc' => true,
		'phpdoc_order' => true,
		'phpdoc_return_self_reference' => true,
		'phpdoc_scalar' => true,
		'phpdoc_separation' => true,
		'phpdoc_single_line_var_spacing' => true,
		'phpdoc_summary' => true,
		'phpdoc_to_comment' => true,
		'phpdoc_trim' => true,
		'phpdoc_types' => true,
		'phpdoc_types_order' => true,
		'phpdoc_var_without_name' => true,
		'pow_to_exponentiation' => true,
		'psr4' => true,
		'random_api_migration' => true,
		'return_assignment' => true,
		'return_type_declaration' => ['space_before' => 'one'],
		'self_accessor' => true,
		'semicolon_after_instruction' => true,
		'set_type_to_cast' => true,
		'short_scalar_cast' => true,
		'simplified_null_return' => true,
		'single_blank_line_at_eof' => true,
		'single_blank_line_before_namespace' => true,
		'single_class_element_per_statement' => true,
		'single_import_per_statement' => true,
		'single_line_after_imports' => true,
		'single_line_comment_style' => true,
		'single_quote' => true,
		'space_after_semicolon' => true,
		'standardize_increment' => true,
		'standardize_not_equals' => true,
		'string_line_ending' => true,
		'switch_case_semicolon_to_colon' => true,
		'switch_case_space' => true,
		'ternary_operator_spaces' => true,
		'ternary_to_null_coalescing' => true,
		'trailing_comma_in_multiline_array' => true,
		'trim_array_spaces' => true,
		'unary_operator_spaces' => true,
		'visibility_required' => true,
		'whitespace_after_comma_in_array' => true,
		'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
	])->setFinder(
		PhpCsFixer\Finder::create()->exclude('vendor')->in(__DIR__)
	);

/*
This document has been generated with
https://mlocati.github.io/php-cs-fixer-configurator/
you can change this configuration by importing this YAML code:

whitespace:
  indent: "\t"
fixers:
  align_multiline_comment:
	comment_type: all_multiline
  array_indentation: true
  array_syntax:
	syntax: short
  backtick_to_shell_exec: true
  binary_operator_spaces: true
  blank_line_after_namespace: true
  blank_line_after_opening_tag: true
  blank_line_before_statement:
	statements:
	  - continue
	  - declare
	  - return
	  - throw
	  - try
	  - if
	  - goto
  braces: true
  cast_spaces: true
  class_attributes_separation: true
  class_definition: true
  combine_consecutive_issets: true
  combine_consecutive_unsets: true
  concat_space:
	spacing: one
  declare_equal_normalize:
	space: single
  dir_constant: true
  doctrine_annotation_array_assignment: true
  doctrine_annotation_braces: true
  doctrine_annotation_indentation: true
  doctrine_annotation_spaces: true
  elseif: true
  encoding: true
  ereg_to_preg: true
  escape_implicit_backslashes: true
  explicit_indirect_variable: true
  explicit_string_variable: true
  full_opening_tag: true
  fully_qualified_strict_types: true
  function_declaration: true
  function_to_constant: true
  function_typehint_space: true
  heredoc_to_nowdoc: true
  include: true
  indentation_type: true
  is_null: true
  line_ending: true
  linebreak_after_opening_tag: true
  list_syntax:
	syntax: short
  logical_operators: true
  lowercase_cast: true
  lowercase_constants: true
  lowercase_keywords: true
  lowercase_static_reference: true
  magic_constant_casing: true
  mb_str_functions: true
  method_argument_space: true
  method_chaining_indentation: true
  method_separation: true
  modernize_types_casting: true
  multiline_comment_opening_closing: true
  multiline_whitespace_before_semicolons: true
  native_constant_invocation: true
  native_function_casing: true
  native_function_invocation: true
  no_alias_functions: true
  no_binary_string: true
  no_blank_lines_after_class_opening: true
  no_blank_lines_after_phpdoc: true
  no_break_comment: true
  no_closing_tag: true
  no_empty_phpdoc: true
  no_empty_statement: true
  no_extra_blank_lines: true
  no_homoglyph_names: true
  no_leading_import_slash: true
  no_leading_namespace_whitespace: true
  no_mixed_echo_print: true
  no_multiline_whitespace_around_double_arrow: true
  no_null_property_initialization: true
  no_php4_constructor: true
  no_short_bool_cast: true
  no_short_echo_tag: true
  no_singleline_whitespace_before_semicolons: true
  no_spaces_after_function_name: true
  no_spaces_around_offset: true
  no_spaces_inside_parenthesis: true
  no_superfluous_elseif: true
  no_superfluous_phpdoc_tags: true
  no_trailing_comma_in_list_call: true
  no_trailing_comma_in_singleline_array: true
  no_trailing_whitespace: true
  no_trailing_whitespace_in_comment: true
  no_unneeded_control_parentheses: true
  no_unneeded_curly_braces: true
  no_unneeded_final_method: true
  no_unreachable_default_argument_value: true
  no_unused_imports: true
  no_useless_else: true
  no_useless_return: true
  no_whitespace_before_comma_in_array: true
  no_whitespace_in_blank_line: true
  normalize_index_brace: true
  not_operator_with_successor_space: true
  object_operator_without_whitespace: true
  ordered_class_elements: true
  ordered_imports: true
  php_unit_construct: true
  php_unit_dedicate_assert: true
  php_unit_expectation: true
  php_unit_fqcn_annotation: true
  php_unit_namespaced: true
  php_unit_no_expectation_annotation: true
  php_unit_test_annotation: true
  phpdoc_add_missing_param_annotation:
	only_untyped: true
  phpdoc_align: true
  phpdoc_annotation_without_dot: true
  phpdoc_indent: true
  phpdoc_inline_tag: true
  phpdoc_no_access: true
  phpdoc_no_alias_tag: true
  phpdoc_no_empty_return: true
  phpdoc_no_package: true
  phpdoc_no_useless_inheritdoc: true
  phpdoc_order: true
  phpdoc_return_self_reference: true
  phpdoc_scalar: true
  phpdoc_separation: true
  phpdoc_single_line_var_spacing: true
  phpdoc_summary: true
  phpdoc_to_comment: true
  phpdoc_trim: true
  phpdoc_types: true
  phpdoc_types_order: true
  phpdoc_var_without_name: true
  pow_to_exponentiation: true
  psr4: true
  random_api_migration: true
  return_assignment: true
  return_type_declaration:
	space_before: one
  self_accessor: true
  semicolon_after_instruction: true
  set_type_to_cast: true
  short_scalar_cast: true
  simplified_null_return: true
  single_blank_line_at_eof: true
  single_blank_line_before_namespace: true
  single_class_element_per_statement: true
  single_import_per_statement: true
  single_line_after_imports: true
  single_line_comment_style: true
  single_quote: true
  space_after_semicolon: true
  standardize_increment: true
  standardize_not_equals: true
  string_line_ending: true
  switch_case_semicolon_to_colon: true
  switch_case_space: true
  ternary_operator_spaces: true
  ternary_to_null_coalescing: true
  trailing_comma_in_multiline_array: true
  trim_array_spaces: true
  unary_operator_spaces: true
  visibility_required: true
  whitespace_after_comma_in_array: true
  yoda_style:
	equal: false
	identical: false
	less_and_greater: false
risky: true

 */
