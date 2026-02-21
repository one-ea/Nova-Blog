/**
 * Flavor Theme - esbuild 构建脚本
 * 独立 minify 每个文件，保持 WordPress enqueue 粒度
 */
import { build, context } from 'esbuild';
import { readdir, cp, mkdir } from 'node:fs/promises';
import { resolve, join } from 'node:path';

const ASSETS_DIR = resolve('assets');
const DIST_DIR = resolve('assets/dist');
const isWatch = process.argv.includes('--watch');

// JS 文件列表
const jsFiles = (await readdir(join(ASSETS_DIR, 'js')))
  .filter(f => f.endsWith('.js'));

// CSS 文件列表
const cssFiles = (await readdir(join(ASSETS_DIR, 'css')))
  .filter(f => f.endsWith('.css'));

// 公共配置
const sharedConfig = {
  bundle: false,
  minify: true,
  sourcemap: false,
  target: ['es2020'],
  logLevel: 'info',
};

// 构建入口
const entryPoints = [
  ...jsFiles.map(f => join(ASSETS_DIR, 'js', f)),
  ...cssFiles.map(f => join(ASSETS_DIR, 'css', f)),
];

const buildConfig = {
  ...sharedConfig,
  entryPoints,
  outdir: DIST_DIR,
  // 保持 js/ 和 css/ 子目录结构
  outbase: ASSETS_DIR,
};

if (isWatch) {
  const ctx = await context(buildConfig);
  await ctx.watch();
  console.log('👀 Watching for changes...');
} else {
  const result = await build(buildConfig);
  // 复制 fonts 目录到 dist（CSS 相对路径 ../fonts/ 需要）
  await mkdir(join(DIST_DIR, 'fonts'), { recursive: true });
  await cp(join(ASSETS_DIR, 'fonts'), join(DIST_DIR, 'fonts'), { recursive: true });
  const total = jsFiles.length + cssFiles.length;
  console.log(`✅ ${total} files minified → assets/dist/`);
}
