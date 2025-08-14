(function(blocks, element, editor, components){
  const { registerBlockType } = blocks;
  const { createElement: h, useState, useEffect } = element;
  const { InspectorControls } = editor;
  const { PanelBody, SelectControl, TextControl } = components;

  registerBlockType('tih/today', {
    attributes: {
      type: { type: 'string', default: 'events' },
      month: { type: 'number', default: 0 },
      day: { type: 'number', default: 0 },
      limit: { type: 'number', default: 10 }
    },
    edit: (props) => {
      const { attributes, setAttributes } = props;
      const [data, setData] = useState(null);

      function buildQuery() {
        const q = new URLSearchParams();
        if (attributes.type) q.set('type', attributes.type);
        if (attributes.month) q.set('month', attributes.month);
        if (attributes.day) q.set('day', attributes.day);
        if (attributes.limit) q.set('limit', attributes.limit);
        return q.toString();
      }

      useEffect(() => {
        fetch(`/wp-json/tih/v1/day?${buildQuery()}`)
          .then(r=>r.json()).then(setData).catch(()=>setData(null));
      }, [attributes.type, attributes.month, attributes.day, attributes.limit]);

      return h('div', {},
        h(InspectorControls, {},
          h(PanelBody, { title: 'Settings' },
            h(SelectControl, {
              label: 'Type',
              value: attributes.type,
              options: [
                { label: 'Events', value: 'events' },
                { label: 'Births', value: 'births' },
                { label: 'Deaths', value: 'deaths' }
              ],
              onChange: (val)=> setAttributes({ type: val })
            }),
            h(TextControl, {
              label: 'Month (1-12, empty = today)',
              type: 'number',
              value: attributes.month || '',
              onChange: (val)=> setAttributes({ month: Number(val)||0 })
            }),
            h(TextControl, {
              label: 'Day (1-31, empty = today)',
              type: 'number',
              value: attributes.day || '',
              onChange: (val)=> setAttributes({ day: Number(val)||0 })
            }),
            h(TextControl, {
              label: 'Limit',
              type: 'number',
              value: attributes.limit,
              onChange: (val)=> setAttributes({ limit: Math.max(1, Number(val)||10) })
            }),
          )
        ),
        h('div', { className: 'tih-preview' },
          data ? [
            h('h4', {}, `Today in History — ${data.date} (${data.type})`),
            h('ul', { className: 'tih-list' },
              (data.items||[]).map((it, i)=>
                h('li', { key:i, className: 'tih-item' },
                  h('strong', { className:'tih-year' }, it.year || ''),
                  ' ',
                  h('span', { className:'tih-desc' }, it.description || '')
                )
              )
            )
          ] : 'Loading…'
        )
      );
    },
    save: () => null
  });
})(window.wp.blocks, window.wp.element, window.wp.editor || window.wp.blockEditor, window.wp.components);
