module.exports = {
  "plugins": ["@babel/plugin-syntax-dynamic-import"],
  "presets": [
    ["@babel/preset-env", {
      "targets": {
        "ie": 11
      },
      "modules": false,
      "loose": true,
      "useBuiltIns": 'usage'
    }]
  ]
}
