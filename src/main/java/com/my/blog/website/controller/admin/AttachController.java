package com.my.blog.website.controller.admin;

import com.github.pagehelper.PageInfo;
import com.my.blog.website.constant.WebConst;
import com.my.blog.website.controller.BaseController;
import com.my.blog.website.dto.LogActions;
import com.my.blog.website.dto.Types;
import com.my.blog.website.exception.TipException;
import com.my.blog.website.modal.Bo.RestResponseBo;
import com.my.blog.website.modal.Vo.AttachVo;
import com.my.blog.website.modal.Vo.UserVo;
import com.my.blog.website.service.IAttachService;
import com.my.blog.website.service.ILogService;
import com.my.blog.website.utils.Commons;
import com.my.blog.website.utils.TaleUtils;
import com.my.blog.website.utils.UUID;

import org.apache.tomcat.util.http.fileupload.IOUtils;
import org.json.JSONObject;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Controller;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.util.FileCopyUtils;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;

import javax.annotation.Resource;
import javax.imageio.ImageIO;
import javax.servlet.http.HttpServletRequest;

import java.awt.image.BufferedImage;
import java.io.File;
import java.io.FileOutputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

/**
 * 附件管理
 *
 * Created by 13 on 2017/2/21.
 */
@Controller
@RequestMapping("admin/attach")
public class AttachController extends BaseController {

    private static final Logger LOGGER = LoggerFactory.getLogger(AttachController.class);

    public static final String CLASSPATH = TaleUtils.getUplodFilePath();

    @Resource
    private IAttachService attachService;

    @Resource
    private ILogService logService;

    /**
     * 附件页面
     *
     * @param request
     * @param page
     * @param limit
     * @return
     */
    @GetMapping(value = "")
    public String index(HttpServletRequest request, @RequestParam(value = "page", defaultValue = "1") int page,
                        @RequestParam(value = "limit", defaultValue = "12") int limit) {
        PageInfo<AttachVo> attachPaginator = attachService.getAttachs(page, limit);
        request.setAttribute("attachs", attachPaginator);
        request.setAttribute(Types.ATTACH_URL.getType(), Commons.site_option(Types.ATTACH_URL.getType(), Commons.site_url()));
        request.setAttribute("max_file_size", WebConst.MAX_FILE_SIZE / 1024);
        return "admin/attach";
    }

    /**
     * 上传文件接口
     *
     * @param request
     * @return
     */
    @PostMapping(value = "upload")
    @ResponseBody
    @Transactional(rollbackFor = TipException.class)
    public RestResponseBo upload(HttpServletRequest request, @RequestParam("file") MultipartFile[] multipartFiles) throws IOException {
        String webRootPath=request.getServletContext().getRealPath("");
        UserVo users = this.user(request);
        Integer uid = users.getUid();
        List<String> errorFiles = new ArrayList<>();
        try {
            for (MultipartFile multipartFile : multipartFiles) {
                String fname = multipartFile.getOriginalFilename();
                if (multipartFile.getSize() <= WebConst.MAX_FILE_SIZE) {
                    String ftype = TaleUtils.isImage(multipartFile.getInputStream()) ? Types.IMAGE.getType() : Types.FILE.getType();
                    String fkey = TaleUtils.getFileKey(webRootPath,fname,ftype);
                    File file = new File(webRootPath+fkey);
                    try {
                        FileCopyUtils.copy(multipartFile.getInputStream(),new FileOutputStream(file));
                    } catch (IOException e) {
                        e.printStackTrace();
                    }
                    attachService.save(fname, fkey, ftype, uid);
                } else {
                    errorFiles.add(fname);
                }
            }
        } catch (Exception e) {
            return RestResponseBo.fail();
        }
        return RestResponseBo.ok(errorFiles);
    }
    
    @PostMapping(value = "uploadImg")
	@ResponseBody
	@Transactional(rollbackFor = TipException.class)
    public String uploadImg(HttpServletRequest request, @RequestParam("imgFile") MultipartFile zipFile) {
		UserVo users = this.user(request);
		Integer uid = users.getUid();
		JSONObject res = new JSONObject();
		try {
			String webRootPath=request.getServletContext().getRealPath("");
			String fname = zipFile.getOriginalFilename();
			String ftype = TaleUtils.isImage(zipFile.getInputStream()) ? Types.IMAGE.getType() : Types.FILE.getType();
			String fkey = TaleUtils.getFileKey(webRootPath,fname,ftype);
			File targetFile = new File(webRootPath+fkey);
			try(FileOutputStream fileOutputStream = new FileOutputStream(targetFile)) {
				IOUtils.copy(zipFile.getInputStream(), fileOutputStream);
			} catch (IOException e) {
				e.printStackTrace();
			}
			attachService.save(fname, fkey, ftype, uid);
			//{"thumbURL":"\/uploads\/image\/201912\/18\/20191218165027_79943.png","oriURL":"\/uploads\/image\/201912\/18\/20191218165027_79943.png","filesize":19974,"width":170,"height":138}
			try(FileWriter fw = new FileWriter(webRootPath+new File("/php/default/db/data/image.db"),true);){
				BufferedImage read = ImageIO.read(targetFile);
				read.getData().getHeight();
				;
				JSONObject record = new JSONObject();
				record.put("thumbURL", fkey);
				record.put("oriURL", fkey);
				record.put("filesize", targetFile.length());
				record.put("width", read.getData().getWidth());
				record.put("height", read.getData().getHeight());
				fw.write(record.toString()+"\n");
				fw.flush();
			} catch (IOException e) {
				e.printStackTrace();
			}
			
			res.put("url", fkey);
		}catch(Exception ex) {
			ex.printStackTrace();
		}
		return res.toString();
    }

    /**
     * 删除附件
     * @param id
     * @param request
     * @return
     */
    @RequestMapping(value = "delete")
    @ResponseBody
    @Transactional(rollbackFor = TipException.class)
    public RestResponseBo delete(@RequestParam Integer id, HttpServletRequest request) {
        try {
            AttachVo attach = attachService.selectById(id);
            if (null == attach) return RestResponseBo.fail("不存在该附件");
            attachService.deleteById(id);
            new File(request.getServletContext().getRealPath("")+attach.getFkey()).delete();
            logService.insertLog(LogActions.DEL_ARTICLE.getAction(), attach.getFkey(), request.getRemoteAddr(), this.getUid(request));
        } catch (Exception e) {
            String msg = "附件删除失败";
            if (e instanceof TipException) msg = e.getMessage();
            else LOGGER.error(msg, e);
            return RestResponseBo.fail(msg);
        }
        return RestResponseBo.ok();
    }

}
