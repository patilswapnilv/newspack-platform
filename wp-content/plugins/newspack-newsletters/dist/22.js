(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[22],{

/***/ "./node_modules/codemirror/addon/mode/simple.js":
/*!******************************************************!*\
  !*** ./node_modules/codemirror/addon/mode/simple.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// CodeMirror, copyright (c) by Marijn Haverbeke and others\n// Distributed under an MIT license: https://codemirror.net/LICENSE\n\n(function(mod) {\n  if (true) // CommonJS\n    mod(__webpack_require__(/*! ../../lib/codemirror */ \"./node_modules/codemirror/lib/codemirror.js\"));\n  else {}\n})(function(CodeMirror) {\n  \"use strict\";\n\n  CodeMirror.defineSimpleMode = function(name, states) {\n    CodeMirror.defineMode(name, function(config) {\n      return CodeMirror.simpleMode(config, states);\n    });\n  };\n\n  CodeMirror.simpleMode = function(config, states) {\n    ensureState(states, \"start\");\n    var states_ = {}, meta = states.meta || {}, hasIndentation = false;\n    for (var state in states) if (state != meta && states.hasOwnProperty(state)) {\n      var list = states_[state] = [], orig = states[state];\n      for (var i = 0; i < orig.length; i++) {\n        var data = orig[i];\n        list.push(new Rule(data, states));\n        if (data.indent || data.dedent) hasIndentation = true;\n      }\n    }\n    var mode = {\n      startState: function() {\n        return {state: \"start\", pending: null,\n                local: null, localState: null,\n                indent: hasIndentation ? [] : null};\n      },\n      copyState: function(state) {\n        var s = {state: state.state, pending: state.pending,\n                 local: state.local, localState: null,\n                 indent: state.indent && state.indent.slice(0)};\n        if (state.localState)\n          s.localState = CodeMirror.copyState(state.local.mode, state.localState);\n        if (state.stack)\n          s.stack = state.stack.slice(0);\n        for (var pers = state.persistentStates; pers; pers = pers.next)\n          s.persistentStates = {mode: pers.mode,\n                                spec: pers.spec,\n                                state: pers.state == state.localState ? s.localState : CodeMirror.copyState(pers.mode, pers.state),\n                                next: s.persistentStates};\n        return s;\n      },\n      token: tokenFunction(states_, config),\n      innerMode: function(state) { return state.local && {mode: state.local.mode, state: state.localState}; },\n      indent: indentFunction(states_, meta)\n    };\n    if (meta) for (var prop in meta) if (meta.hasOwnProperty(prop))\n      mode[prop] = meta[prop];\n    return mode;\n  };\n\n  function ensureState(states, name) {\n    if (!states.hasOwnProperty(name))\n      throw new Error(\"Undefined state \" + name + \" in simple mode\");\n  }\n\n  function toRegex(val, caret) {\n    if (!val) return /(?:)/;\n    var flags = \"\";\n    if (val instanceof RegExp) {\n      if (val.ignoreCase) flags = \"i\";\n      val = val.source;\n    } else {\n      val = String(val);\n    }\n    return new RegExp((caret === false ? \"\" : \"^\") + \"(?:\" + val + \")\", flags);\n  }\n\n  function asToken(val) {\n    if (!val) return null;\n    if (val.apply) return val\n    if (typeof val == \"string\") return val.replace(/\\./g, \" \");\n    var result = [];\n    for (var i = 0; i < val.length; i++)\n      result.push(val[i] && val[i].replace(/\\./g, \" \"));\n    return result;\n  }\n\n  function Rule(data, states) {\n    if (data.next || data.push) ensureState(states, data.next || data.push);\n    this.regex = toRegex(data.regex);\n    this.token = asToken(data.token);\n    this.data = data;\n  }\n\n  function tokenFunction(states, config) {\n    return function(stream, state) {\n      if (state.pending) {\n        var pend = state.pending.shift();\n        if (state.pending.length == 0) state.pending = null;\n        stream.pos += pend.text.length;\n        return pend.token;\n      }\n\n      if (state.local) {\n        if (state.local.end && stream.match(state.local.end)) {\n          var tok = state.local.endToken || null;\n          state.local = state.localState = null;\n          return tok;\n        } else {\n          var tok = state.local.mode.token(stream, state.localState), m;\n          if (state.local.endScan && (m = state.local.endScan.exec(stream.current())))\n            stream.pos = stream.start + m.index;\n          return tok;\n        }\n      }\n\n      var curState = states[state.state];\n      for (var i = 0; i < curState.length; i++) {\n        var rule = curState[i];\n        var matches = (!rule.data.sol || stream.sol()) && stream.match(rule.regex);\n        if (matches) {\n          if (rule.data.next) {\n            state.state = rule.data.next;\n          } else if (rule.data.push) {\n            (state.stack || (state.stack = [])).push(state.state);\n            state.state = rule.data.push;\n          } else if (rule.data.pop && state.stack && state.stack.length) {\n            state.state = state.stack.pop();\n          }\n\n          if (rule.data.mode)\n            enterLocalMode(config, state, rule.data.mode, rule.token);\n          if (rule.data.indent)\n            state.indent.push(stream.indentation() + config.indentUnit);\n          if (rule.data.dedent)\n            state.indent.pop();\n          var token = rule.token\n          if (token && token.apply) token = token(matches)\n          if (matches.length > 2 && rule.token && typeof rule.token != \"string\") {\n            for (var j = 2; j < matches.length; j++)\n              if (matches[j])\n                (state.pending || (state.pending = [])).push({text: matches[j], token: rule.token[j - 1]});\n            stream.backUp(matches[0].length - (matches[1] ? matches[1].length : 0));\n            return token[0];\n          } else if (token && token.join) {\n            return token[0];\n          } else {\n            return token;\n          }\n        }\n      }\n      stream.next();\n      return null;\n    };\n  }\n\n  function cmp(a, b) {\n    if (a === b) return true;\n    if (!a || typeof a != \"object\" || !b || typeof b != \"object\") return false;\n    var props = 0;\n    for (var prop in a) if (a.hasOwnProperty(prop)) {\n      if (!b.hasOwnProperty(prop) || !cmp(a[prop], b[prop])) return false;\n      props++;\n    }\n    for (var prop in b) if (b.hasOwnProperty(prop)) props--;\n    return props == 0;\n  }\n\n  function enterLocalMode(config, state, spec, token) {\n    var pers;\n    if (spec.persistent) for (var p = state.persistentStates; p && !pers; p = p.next)\n      if (spec.spec ? cmp(spec.spec, p.spec) : spec.mode == p.mode) pers = p;\n    var mode = pers ? pers.mode : spec.mode || CodeMirror.getMode(config, spec.spec);\n    var lState = pers ? pers.state : CodeMirror.startState(mode);\n    if (spec.persistent && !pers)\n      state.persistentStates = {mode: mode, spec: spec.spec, state: lState, next: state.persistentStates};\n\n    state.localState = lState;\n    state.local = {mode: mode,\n                   end: spec.end && toRegex(spec.end),\n                   endScan: spec.end && spec.forceEnd !== false && toRegex(spec.end, false),\n                   endToken: token && token.join ? token[token.length - 1] : token};\n  }\n\n  function indexOf(val, arr) {\n    for (var i = 0; i < arr.length; i++) if (arr[i] === val) return true;\n  }\n\n  function indentFunction(states, meta) {\n    return function(state, textAfter, line) {\n      if (state.local && state.local.mode.indent)\n        return state.local.mode.indent(state.localState, textAfter, line);\n      if (state.indent == null || state.local || meta.dontIndentStates && indexOf(state.state, meta.dontIndentStates) > -1)\n        return CodeMirror.Pass;\n\n      var pos = state.indent.length - 1, rules = states[state.state];\n      scan: for (;;) {\n        for (var i = 0; i < rules.length; i++) {\n          var rule = rules[i];\n          if (rule.data.dedent && rule.data.dedentIfLineStart !== false) {\n            var m = rule.regex.exec(textAfter);\n            if (m && m[0]) {\n              pos--;\n              if (rule.next || rule.push) rules = states[rule.next || rule.push];\n              textAfter = textAfter.slice(m[0].length);\n              continue scan;\n            }\n          }\n        }\n        break;\n      }\n      return pos < 0 ? 0 : state.indent[pos];\n    };\n  }\n});\n\n\n//# sourceURL=webpack:///./node_modules/codemirror/addon/mode/simple.js?");

/***/ }),

/***/ "./node_modules/codemirror/mode/wast/wast.js":
/*!***************************************************!*\
  !*** ./node_modules/codemirror/mode/wast/wast.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// CodeMirror, copyright (c) by Marijn Haverbeke and others\n// Distributed under an MIT license: https://codemirror.net/LICENSE\n\n(function(mod) {\n  if (true) // CommonJS\n    mod(__webpack_require__(/*! ../../lib/codemirror */ \"./node_modules/codemirror/lib/codemirror.js\"), __webpack_require__(/*! ../../addon/mode/simple */ \"./node_modules/codemirror/addon/mode/simple.js\"));\n  else {}\n})(function(CodeMirror) {\n\"use strict\";\n\nvar kKeywords = [\n    \"align\",\n    \"block\",\n    \"br(_if|_table|_on_(cast|data|func|i31|null))?\",\n    \"call(_indirect|_ref)?\",\n    \"current_memory\",\n    \"\\\\bdata\\\\b\",\n    \"drop\",\n    \"elem\",\n    \"else\",\n    \"end\",\n    \"export\",\n    \"\\\\bextern\\\\b\",\n    \"\\\\bfunc\\\\b\",\n    \"global(\\\\.(get|set))?\",\n    \"if\",\n    \"import\",\n    \"local(\\\\.(get|set|tee))?\",\n    \"loop\",\n    \"module\",\n    \"mut\",\n    \"nop\",\n    \"offset\",\n    \"param\",\n    \"result\",\n    \"return(_call(_indirect|_ref)?)?\",\n    \"select\",\n    \"start\",\n    \"table(\\\\.(size|get|set|size|grow|fill|init|copy))?\",\n    \"then\",\n    \"type\",\n    \"unreachable\",\n\n    // Numeric opcodes.\n    \"i(32|64)\\\\.(store(8|16)|(load(8|16)_[su]))\",\n    \"i64\\\\.(load32_[su]|store32)\",\n    \"[fi](32|64)\\\\.(const|load|store)\",\n    \"f(32|64)\\\\.(abs|add|ceil|copysign|div|eq|floor|[gl][et]|max|min|mul|nearest|neg?|sqrt|sub|trunc)\",\n    \"i(32|64)\\\\.(a[dn]d|c[lt]z|(div|rem)_[su]|eqz?|[gl][te]_[su]|mul|ne|popcnt|rot[lr]|sh(l|r_[su])|sub|x?or)\",\n    \"i64\\\\.extend_[su]_i32\",\n    \"i32\\\\.wrap_i64\",\n    \"i(32|64)\\\\.trunc_f(32|64)_[su]\",\n    \"f(32|64)\\\\.convert_i(32|64)_[su]\",\n    \"f64\\\\.promote_f32\",\n    \"f32\\\\.demote_f64\",\n    \"f32\\\\.reinterpret_i32\",\n    \"i32\\\\.reinterpret_f32\",\n    \"f64\\\\.reinterpret_i64\",\n    \"i64\\\\.reinterpret_f64\",\n    // Atomics.\n    \"memory(\\\\.((atomic\\\\.(notify|wait(32|64)))|grow|size))?\",\n    \"i64\\.atomic\\\\.(load32_u|store32|rmw32\\\\.(a[dn]d|sub|x?or|(cmp)?xchg)_u)\",\n    \"i(32|64)\\\\.atomic\\\\.(load((8|16)_u)?|store(8|16)?|rmw(\\\\.(a[dn]d|sub|x?or|(cmp)?xchg)|(8|16)\\\\.(a[dn]d|sub|x?or|(cmp)?xchg)_u))\",\n    // SIMD.\n    \"v128\\\\.load(8x8|16x4|32x2)_[su]\",\n    \"v128\\\\.load(8|16|32|64)_splat\",\n    \"v128\\\\.(load|store)(8|16|32|64)_lane\",\n    \"v128\\\\.load(32|64)_zero\",\n    \"v128\\.(load|store|const|not|andnot|and|or|xor|bitselect|any_true)\",\n    \"i(8x16|16x8)\\\\.(extract_lane_[su]|(add|sub)_sat_[su]|avgr_u)\",\n    \"i(8x16|16x8|32x4|64x2)\\\\.(neg|add|sub|abs|shl|shr_[su]|all_true|bitmask|eq|ne|[lg][te]_s)\",\n    \"(i(8x16|16x8|32x4|64x2)|f(32x4|64x2))\\.(splat|replace_lane)\",\n    \"i(8x16|16x8|32x4)\\\\.(([lg][te]_u)|((min|max)_[su]))\",\n    \"f(32x4|64x2)\\\\.(neg|add|sub|abs|nearest|eq|ne|[lg][te]|sqrt|mul|div|min|max|ceil|floor|trunc)\",\n    \"[fi](32x4|64x2)\\\\.extract_lane\",\n    \"i8x16\\\\.(shuffle|swizzle|popcnt|narrow_i16x8_[su])\",\n    \"i16x8\\\\.(narrow_i32x4_[su]|mul|extadd_pairwise_i8x16_[su]|q15mulr_sat_s)\",\n    \"i16x8\\\\.(extend|extmul)_(low|high)_i8x16_[su]\",\n    \"i32x4\\\\.(mul|dot_i16x8_s|trunc_sat_f64x2_[su]_zero)\",\n    \"i32x4\\\\.((extend|extmul)_(low|high)_i16x8_|trunc_sat_f32x4_|extadd_pairwise_i16x8_)[su]\",\n    \"i64x2\\\\.(mul|(extend|extmul)_(low|high)_i32x4_[su])\",\n    \"f32x4\\\\.(convert_i32x4_[su]|demote_f64x2_zero)\",\n    \"f64x2\\\\.(promote_low_f32x4|convert_low_i32x4_[su])\",\n    // Reference types, function references, and GC.\n    \"\\\\bany\\\\b\",\n    \"array\\\\.len\",\n    \"(array|struct)(\\\\.(new_(default_)?with_rtt|get(_[su])?|set))?\",\n    \"\\\\beq\\\\b\",\n    \"field\",\n    \"i31\\\\.(new|get_[su])\",\n    \"\\\\bnull\\\\b\",\n    \"ref(\\\\.(([ai]s_(data|func|i31))|cast|eq|func|(is_|as_non_)?null|test))?\",\n    \"rtt(\\\\.(canon|sub))?\",\n];\n\nCodeMirror.defineSimpleMode('wast', {\n  start: [\n    {regex: /[+\\-]?(?:nan(?::0x[0-9a-fA-F]+)?|infinity|inf|0x[0-9a-fA-F]+\\.?[0-9a-fA-F]*p[+\\/-]?\\d+|\\d+(?:\\.\\d*)?[eE][+\\-]?\\d*|\\d+\\.\\d*|0x[0-9a-fA-F]+|\\d+)/, token: \"number\"},\n    {regex: new RegExp(kKeywords.join('|')), token: \"keyword\"},\n    {regex: /\\b((any|data|eq|extern|i31|func)ref|[fi](32|64)|i(8|16))\\b/, token: \"atom\"},\n    {regex: /\\$([a-zA-Z0-9_`\\+\\-\\*\\/\\\\\\^~=<>!\\?@#$%&|:\\.]+)/, token: \"variable-2\"},\n    {regex: /\"(?:[^\"\\\\\\x00-\\x1f\\x7f]|\\\\[nt\\\\'\"]|\\\\[0-9a-fA-F][0-9a-fA-F])*\"/, token: \"string\"},\n    {regex: /\\(;.*?/, token: \"comment\", next: \"comment\"},\n    {regex: /;;.*$/, token: \"comment\"},\n    {regex: /\\(/, indent: true},\n    {regex: /\\)/, dedent: true},\n  ],\n\n  comment: [\n    {regex: /.*?;\\)/, token: \"comment\", next: \"start\"},\n    {regex: /.*/, token: \"comment\"},\n  ],\n\n  meta: {\n    dontIndentStates: ['comment'],\n  },\n});\n\n// https://github.com/WebAssembly/design/issues/981 mentions text/webassembly,\n// which seems like a reasonable choice, although it's not standard right now.\nCodeMirror.defineMIME(\"text/webassembly\", \"wast\");\n\n});\n\n\n//# sourceURL=webpack:///./node_modules/codemirror/mode/wast/wast.js?");

/***/ })

}]);