<?php

namespace IlBronza\FormField\Tests;

use IlBronza\FormField\Fields\SelectFormField;
use IlBronza\FormField\Fields\SelectInputFormField;
use IlBronza\FormField\Fields\TextFormField;
use IlBronza\FormField\Fields\DateFormField;
use IlBronza\FormField\Fields\BooleanCheckboxFormField;
use IlBronza\FormField\FormFieldServiceProvider;
use Orchestra\Testbench\TestCase;

class SelectOptionsRouteTest extends TestCase
{
	protected function getPackageProviders($app) : array
	{
		return [FormFieldServiceProvider::class];
	}

	private function renderSelect(array $parameters = [], string $class = SelectFormField::class) : string
	{
		$field = new $class(array_merge([
			'name' => 'vehicle_id',
			'type' => 'select',
			'list' => [0 => 'Zero', 7 => 'Golf'],
			'value' => 0,
			'showLabel' => false,
			'showSelectPlaceholderText' => false,
			'data' => ['custom' => 'kept'],
		], $parameters));

		$field->executeBeforeRenderingOperations();

		return view('formfield::uikit._select', ['field' => $field])->render();
	}

	public function test_configured_route_reaches_the_select_and_keeps_initial_values() : void
	{
		$html = $this->renderSelect(['possibleValuesRoute' => '/vehicles/options?order=42&active=1']);

		$this->assertStringContainsString('data-possible-values-route="/vehicles/options?order=42&amp;active=1"', $html);
		$this->assertStringContainsString('data-custom="kept"', $html);
		$this->assertMatchesRegularExpression('/<option value="0"\s+selected\s*>\s*Zero/', $html);
		$this->assertStringContainsString('Golf', $html);
	}

	public function test_unconfigured_or_non_editable_selects_do_not_expose_the_loader() : void
	{
		foreach ([[], ['readOnly' => true], ['disabled' => true]] as $parameters)
		{
			if ($parameters)
				$parameters['possibleValuesRoute'] = '/vehicles/options';

			$this->assertStringNotContainsString('data-possible-values-route=', $this->renderSelect($parameters));
		}
	}

	public function test_native_multiple_and_select_input_fields_inherit_the_route() : void
	{
		$html = $this->renderSelect([
			'possibleValuesRoute' => '/vehicles/options',
			'select2' => false,
			'multiple' => true,
			'value' => [0, 7],
		], SelectInputFormField::class);

		$this->assertStringContainsString('data-possible-values-route="/vehicles/options"', $html);
		$this->assertStringContainsString('name="vehicle_id[]"', $html);
		$this->assertStringContainsString('multiple', $html);
		$this->assertStringContainsString('ibuseselectmanualinput', $html);
	}

	public function test_select_exposes_both_its_context_and_its_refresh_targets() : void
	{
		$html = $this->renderSelect([
			'possibleValuesRoute' => '/vehicles/options',
			'possibleValuesRouteFields' => ['operator_id', 'starts_at'],
			'refreshSelectOptions' => ['task_id', 'other_id'],
		]);

		$this->assertStringContainsString('data-possible-values-route-fields="[&quot;operator_id&quot;,&quot;starts_at&quot;]"', $html);
		$this->assertStringContainsString('data-refresh-select-options="[&quot;task_id&quot;,&quot;other_id&quot;]"', $html);
		$this->assertStringContainsString('data-custom="kept"', $html);
	}

	public function test_text_date_and_boolean_checkbox_fields_render_the_refresh_trigger() : void
	{
		foreach ([TextFormField::class => 'text', DateFormField::class => 'date', BooleanCheckboxFormField::class => 'booleanCheckbox'] as $class => $type)
		{
			$field = new $class([
				'name' => 'source',
				'type' => $type,
				'value' => $type === 'booleanCheckbox' ? true : ($type === 'date' ? '2026-10-15' : 'test'),
				'showLabel' => false,
				'refreshSelectOptions' => ['task_id', 'other_id'],
			]);

			$html = view('formfield::uikit._' . $type, ['field' => $field])->render();

			$this->assertStringContainsString('data-refresh-select-options="[&quot;task_id&quot;,&quot;other_id&quot;]"', $html);
			$this->assertStringNotContainsString('data-possible-values-route=', $html);
		}
	}
}
