// Customizer 实时预览
(function(api) {
    api('flavor_seed_color', function(value) {
        value.bind(function(newSeedColor) {
            if (window.ColorEngine) {
                ColorEngine.applyTheme(newSeedColor);
            }
        });
    });
})(wp.customize);
