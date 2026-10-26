<script>
    jQuery(function ($) {
        const container = document.getElementsByClassName(@json('fieldcontainer' . $field->getId()))[0];
        if (!container) return;

        const name = @json($field->getName());
        const sources = Array.from(container.querySelectorAll('input, select, textarea'))
            .filter(input => input.name === name);
        if (!sources.length) return;

        const form = sources[0].form;
        const scope = form || container.closest('fieldset');
        if (!scope) return;

        const targetNames = @json($field->getOpenerTargetNames());
        const expected = String(@json($field->getOpenerValue()));
        const required = @json($field->getOpenerRequiredAttribute());

        function refresh() {
            const source = sources[0].type === 'radio' ? sources.find(input => input.checked) : sources[0];
            const value = source?.type === 'checkbox' ? String(source.checked) : source?.value;
            const show = value === expected;
            const inputs = form ? Array.from(form.elements) : Array.from(scope.querySelectorAll('input, select, textarea'));

            inputs.filter(input => targetNames.includes(input.name)).forEach(input => {
                const row = input.closest('.fieldcontainer');
                if (row) row.style.display = show ? '' : 'none';
                if (required) input.required = show;
            });
        }

        $(sources).on('change ibchanged', refresh);
        refresh();
    });
</script>
